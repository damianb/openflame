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

	protected $routes = array();

	public function newRoutes(array $routes)
	{
		foreach($routes as $route)
		{
			$this->newRoute($route['path'], $route['callback']);
		}

		return $this;
	}

	public function newCachedRoutes(array $routes)
	{
		foreach($routes as $route)
		{
			$this->newCachedRoute($route);
		}

		return $this;
	}

	public function getFullRouteCache()
	{
		$route_cache = array();
		foreach($this->routes as $route)
		{
			array_push($route_cache, $route->getSerializedRoute());
		}

		return $route_cache;
	}

	public function newRoute($route_data, $route_callback)
	{
		$route = \OpenFlame\Framework\URL\RouteInstance::newInstance()
			->loadRawRoute($route_data)
			->setRouteCallback($route_callback);

		$this->loadRoute($route, $route->getRouteBase());

		return $this;
	}

	public function newCachedRoute($route_data)
	{
		$route = \OpenFlame\Framework\URL\RouteInstance::newInstance()
			->loadSerializedRoute($route_data);

		$this->loadRoute($route, $route->getRouteBase());
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

	public function processRequest($request_url)
	{
		// Get rid of _GET query string
		if (strpos($request_url, '?') !== false)
		{
			$request_url = substr($request_url, 0, strpos($request_url, '?'));
		}

		list($request_base, ) = explode('/', $request_url, 2);

		// We're cheating a bit here to boost performance under load.
		if(!isset($this->routes[$request_base]))
		{
			// 404 error
			return false;
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
			return false;
		}

		return $route;
	}
}
