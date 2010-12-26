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

	public function __construct()
	{
		// asdf
	}

	public function newRoutes(array $routes)
	{
		foreach($routes as $route)
			$this->newRoute($route);

		return $this;
	}

	public function newRoute($route)
	{
		list($route_base, ) = explode('/', $route, 2);
		$this->routes[$route_base][] = new \OpenFlame\Framework\URL\RouteInstance($route);
		return $this;
	}

	public function processRequest($request_url)
	{
		$request = explode('/', $request_url, self::EXPLODE_LIMIT);

		$request_base = $request[0];
		// We're cheating a bit here to boost performance under load.
		if(!isset($this->routes[$request_base]))
		{
			// throw 404 error here
		}

		// We need to verify the request against the routes one by one, and go for the last one that works.
		// We do this so that routes set later on take priority, so routes can be easily overridden in an application
		foreach(array_reverse($this->routes[$request_base]) as $route)
		{
			if($route->verify($request))
			{
				$found = true;
				break;
			}
		}

		if($found !== true)
		{
			// throw 404 error here
		}

		return $route->loadRequest($request);
	}
}
