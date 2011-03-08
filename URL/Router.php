<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\URL;

if(!defined('OpenFlame\\Framework\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Static URL router,
 * 	     A simple and easy to understand static URL router, provides an alternative to the fluid URL handler.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Router
{
	/**
	 * @const - Just to make sure no one sends stupidly long url requests for this to process
	 * @note - Changes to this setting also affect behavior in OpenFlame\Framework\URL\RouteInstance
	 */
	const EXPLODE_LIMIT = 15;

	protected $base_url = '';

	/**
	 * @var array - An array of the routes we've prepared
	 */
	protected $routes = array();

	/**
	 * @var \OpenFlame\Framework\URL\RouteInstance - The route to return if this is the default landing page.
	 */
	protected $home_route;

	/**
	 * @var \OpenFlame\Framework\URL\RouteInstance - The route to return if no matching route is found.
	 */
	protected $error_route;

	/**
	 * Get the "base URL" of this installation, which is automatically stripped from the beginning of all requests.
	 * @return string - The base URL we are using.
	 */
	public function getBaseURL()
	{
		return $this->base_url;
	}

	/**
	 * Set the "base URL" for this installation, which will be stripped from the beginning of all requests.
	 * @param string $base_url - The "base URL" which we're going to strip
	 * @return \OpenFlame\Framework\URL\Router - Provides a fluent interface.
	 */
	public function setBaseURL($base_url)
	{
		$this->base_url = rtrim($base_url, '/'); // We don't want a trailing slash here.

		return $this;
	}

	/**
	 * Create a new route with a specific route path to cover, and a callback to assign to the route on match
	 * @param string $route_data - The formatted route path to use for this route.
	 * @param callable $route_callback - The callback to use when we've got the right route.
	 * @return \OpenFlame\Framework\URL\RouteInstance - The newly created route.
	 */
	public function newRoute($route_data, $route_callback)
	{
		$route = \OpenFlame\Framework\URL\RouteInstance::newInstance()
			->loadRawRoute($route_data)
			->setRouteCallback($route_callback);

		return $route;
	}

	/**
	 * Recreate a previously constructed route using the serialized data cache of the route.
	 * @param string $route_data - The serialized cache data to use to regenerate the route.
	 * @return \OpenFlame\Framework\URL\RouteInstance - The newly created route.
	 */
	public function newCachedRoute($route_data)
	{
		$route = \OpenFlame\Framework\URL\RouteInstance::newInstance()
			->loadSerializedRoute($route_data);

		return $route;
	}

	public function newRoutes(array $routes)
	{
		foreach($routes as $route_data)
		{
			$route = $this->newRoute($route_data['path'], $route_data['callback']);
			$this->loadRoute($route, $route->getRouteBase());
		}

		return $this;
	}

	public function newCachedRoutes(array $routes)
	{
		foreach($routes as $route_data)
		{
			$this->newCachedRoute($route_data);
			$this->loadRoute($route, $route->getRouteBase());
		}

		return $this;
	}

	public function loadRoute(\OpenFlame\Framework\URL\RouteInstance $route, $route_base, $prepend = false)
	{
		if($prepend === true)
		{
			array_unshift($this->routes[(string) $route_base], $route);
		}
		else
		{
			array_push($this->routes[(string) $route_base], $route);
		}

		return $this;
	}

	public function getHomeRoute()
	{
		if($this->home_route === NULL)
		{
			throw new \RuntimeException('Failed to retrieve obtain "home" route; the route has not yet been defined');
		}

		return $this->home_route;
	}

	public function setHomeRoute(\OpenFlame\Framework\URL\RouteInstance $route)
	{
		$this->home_route = $route;

		return $this;
	}

	public function getErrorRoute()
	{
		if($this->error_route === NULL)
		{
			throw new \RuntimeException('Failed to retrieve obtain "error" route; the route has not yet been defined');
		}

		return $this->error_route;
	}

	public function setErrorRoute(\OpenFlame\Framework\URL\RouteInstance $route)
	{
		$this->error_route = $route;

		return $this;
	}

	public function getFullRouteCache()
	{
		$route_cache = array();
		foreach($this->routes as $route)
		{
			array_push($route_cache, $route->getSerializedRoute());
		}

		$cache = array(
			'routes'	=> $route_cache,
			'home'		=> $this->getHomeRoute()->getSerializedRoute(),
			'error'		=> $this->getErrorRoute()->getSerializedRoute(),
		);

		return $cache;
	}

	public function loadFromFullRouteCache(array $cache_array)
	{
		$this->newCachedRoutes($cache_array['routes'])
			->setHomeRoute($this->newCachedRoute($cache_array['home']))
			->setErrorRoute($this->newCachedRoute($cache_array['error']));

		return $this;
	}

	public function processRequest($request_url)
	{
		// Get rid of the _GET stuff.
		if (strpos($request_url, '?') !== false)
		{
			$request_url = substr($request_url, 0, strpos($request_url, '?'));
		}

		// Make sure we've got beginning and ending slashes.
		// @note can't use trim() here, it'll cause an issue on a single slash and suddenly we'd have two slashes
		$request_url = '/' . ltrim(rtrim($request_url, '/') . '/');

		// Trim out the base URL.
		$request_url = stripos($request_url, $this->getBaseURL()) === 0 ? substr($request_url, strlen($this->getBaseURL())) : $request_url;

		if($request_url = '/')
		{
			return $this->getHomeRoute();
		}

		list( , $request_base, ) = explode('/', $request_url, 3);

		// We're cheating a bit here to boost performance under load.
		if(!isset($this->routes[$request_base]))
		{
			// 404 error
			return $this->getErrorRoute();
		}

		// We need to verify the request against the routes one by one, and go for the last one that works.
		// We do this so that routes set later on take priority, so routes can be easily overridden in an application
		$found = false;
		foreach(array_reverse($this->routes[$request_base]) as $route)
		{
			if($route->verify($request_url))
			{
				$found = true;
				break;
			}
		}

		if($found !== true)
		{
			// 404 error
			return $this->getErrorRoute();
		}

		return $route;
	}
}
