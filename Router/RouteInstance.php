<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  router
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Router;
use OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Static URL router route instance,
 * 	     A route instance for the static URL router, provides abstraction of request verification.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note Depends upon AliasRouter via dep injector if route aliases are used.
 */
class RouteInstance
{
	/**
	 * @var array - The array of the various path components that we're using to validate
	 */
	protected $route_map = array();

	/**
	 * @var string - The generated regular expression that represents this route instance.
	 */
	protected $route_regexp = '';

	/**
	 * @var string - The "base" of the route, used to help boost efficiency of the router.
	 */
	protected $route_base = '';

	/**
	 * @var string - The serialized representation of this route instance
	 */
	protected $serialized_route = '';

	/**
	 * @var array - The array of data extracted from the request URL, if this route matches the request.
	 */
	protected $data;

	/**
	 * @var callable - The callback to assign to this route, if the route matches the current request.
	 */
	protected $route_callback;

	/**
	 * @var array - The "types" that we can typecast URL variables as.
	 */
	private static $supported_types = array(
		'str'		=> true,
		'string'	=> true,
		'float'		=> true,
		'int'		=> true,
		'integer'	=> true,
	);

	/**
	 * Create a new instance of this class.
	 * @return \OpenFlame\Framework\Router\RouteInstance - The newly created instance.
	 */
	public static function newInstance()
	{
		return new static();
	}

	/**
	 * Get the route base for this instance.
	 * @return string - The route base for the path this instance covers.
	 */
	public function getRouteBase()
	{
		return $this->route_base;
	}

	/**
	 * Set the route base for this instance.
	 * @param string $base - The route base string.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 */
	protected function setRouteBase($base)
	{
		// reset the serialized route on any changes to the route...
		$this->setSerializedRoute(NULL);
		$this->route_base = $base;
		return $this;
	}

	/**
	 * Get the route component map for this instance.
	 * @return array - The route component array for this instance.
	 */
	public function getRouteMap()
	{
		return $this->route_map;
	}

	/**
	 * Set the route component map for this instance.
	 * @param array $map - An array containing the full route component map.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 */
	protected function setRouteMap(array $map)
	{
		// reset the serialized route on any changes to the route...
		$this->setSerializedRoute(NULL);
		$this->route_map = $map;
		return $this;
	}

	/**
	 * Get the regular expression used for this route instance
	 * @return string - The regular expression of this route instance.
	 */
	public function getRouteRegexp()
	{
		return $this->route_regexp;
	}

	/**
	 * Set the regular expression for this route instance.
	 * @param string $regexp - The regular expression for this route instance.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 */
	protected function setRouteRegexp($regexp)
	{
		// reset the serialized route on any changes to the route...
		$this->setSerializedRoute(NULL);
		$this->route_regexp = $regexp;
		return $this;
	}

	/**
	 * Get the callback assigned to this route instance.
	 * @return callable - The callback assigned to this route instance.
	 */
	public function getRouteCallback()
	{
		return $this->route_callback;
	}

	/**
	 * Assign a callback to this route instance.
	 * @param callable $callback - The callback to assign to this route instance.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 *
	 * @throws \LogicException
	 */
	public function setRouteCallback($callback)
	{
		// reset the serialized route on any changes to the route...
		$this->setSerializedRoute(NULL);

		if(substr($callback, 0, 2) != '::' && substr($callback, -2, 2) != '::')
		{
			if(!is_callable($callback))
			{
				throw new \LogicException('Invalid callback provided for route instance');
			}
		}

		$this->route_callback = $callback;

		return $this;
	}

	/**
	 * Get the serialized representation of this route.
	 * @return string - The serialized string of all necessary data relevant to this specific route.
	 */
	public function getSerializedRoute()
	{
		if(empty($this->serialized_route))
		{
			$route_array = array(
				'route_base'		=> $this->getRouteBase(),
				'route_map'			=> $this->getRouteMap(),
				'route_regexp'		=> $this->getRouteRegexp(),
				'route_callback'	=> $this->getRouteCallback(),
			);

			$this->setSerializedRoute(serialize($route_array));
		}

		return $this->serialized_route;
	}

	/**
	 * Set the serialized representation of this route instance.
	 * @param string $route_string - The serialized representation of the route instance.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 */
	protected function setSerializedRoute($route_string)
	{
		$this->serialized_route = $route_string;
		return $this;
	}

	/**
	 * Get whether a certain "type" of URL variable supports typecasting.
	 * @param string $type - The type of variable to check.
	 * @return boolean - Does this type support casting in the route instance?
	 */
	final public function getTypeSupported($type)
	{
		return isset(self::$supported_types[$type]);
	}

	/**
	 * Load up the route data from the raw route path provided.
	 * @param string $route - The raw route path string to interpret and parse.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 */
	public function loadRawRoute($route)
	{
		$route_data = explode('/', trim($route, '/'), \OpenFlame\Framework\Router\Router::EXPLODE_LIMIT);

		$this->setRouteBase($route_data[0])
			->setRouteMap($this->buildRouteMap($route_data))
			->setRouteRegexp($this->buildRouteRegex($this->getRouteMap()));

		return $this;
	}

	/**
	 * Load up a previously serialized (cached) route instance's data and restore it to like-new condition.
	 * @param string $route_string - The serialized route instance data to restore.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 *
	 * @throws \RuntimeException
	 */
	public function loadSerializedRoute($route_string)
	{
		$route_data = @unserialize($route_string);

		// Protected against bunk data
		if($route_data === false || !isset($route_data['route_base']) || !isset($route_data['route_map']) || !isset($route_data['route_regexp']) || !isset($route_data['route_callback']))
		{
			throw new \RuntimeException('Route unserialization failed, data extracted is invalid or incomplete');
		}

		// Load the route data seamlessly
		$this->setRouteBase($route_data['route_base'])
			->setRouteMap($route_data['route_map'])
			->setRouteRegexp($route_data['route_regexp'])
			->setRouteCallback($route_data['route_callback'])
			->setSerializedRoute($route_string);

		return $this;
	}

	/**
	 * Verify the request URL against this route instance -- does the request match this route?
	 * @param string $request - The request URL to check.
	 * @return boolean - Does the request match this route?
	 */
	public function verify($request)
	{
		$result = preg_match($this->getRouteRegexp(), $request, $matches);
		if(!$result)
		{
			return false;
		}
		else
		{
			// We need to load the matches into the request data property
			$map = $this->getRouteMap();
			// Dump the first entry in this.
			array_shift($matches);
			for($i = 0, $j = 0, $size = sizeof($map); $i < $size; $i++)
			{
				if($map[$i]['type'] == 'static')
				{
					continue;
				}
				if(isset($map[$i]))
				{
					$this->setRequestDataPoint($map[$i]['entry'], $matches[$j]);
					$j++;
				}

			}

			return true;
		}
	}

	/**
	 * Get a request URL variable extracted from the request.
	 * @param string $point - The name of the variable to retrieve.
	 * @return mixed - returns NULL if no such variable, or the variable's data.
	 */
	public function getRequestDataPoint($point)
	{
		if(isset($this->data[(string) $point]))
		{
			return $this->data[(string) $point];
		}
		else
		{
			return NULL;
		}

	}

	/**
	 * Set a request URL variable extracted from the current request.
	 * @param string $point - The name of the variable to store this as.
	 * @param mixed $data - The data to store.
	 * @return \OpenFlame\Framework\Router\RouteInstance - Provides a fluent interface.
	 */
	public function setRequestDataPoint($point, $data)
	{
		$this->data[(string) $point] = $data;

		return $this;
	}

	/**
	 * Trigger the route callback.
	 * @return mixed - The returned data from the callback.
	 *
	 * @throws \LogicException
	 */
	public function fireCallback()
	{
		$callback = $this->getRouteCallback();
		if($callback === NULL)
		{
			throw new \LogicException('Attempted to fire callback when no callback has been set');
		}

		// Check to see if this is a route "alias"
		if(substr($callback, 0, 2) == '::' && substr($callback, -2, 2) == '::')
		{
			$injector = \OpenFlame\Framework\Dependency\Injector::getInstance();
			$alias_router = $injector->get('alias_router');

			$route_alias = substr($callback, 2, strlen($callback) - 4);
			return call_user_func($alias_router->resolveAlias($route_alias), $this);
		}

		return call_user_func($callback, $this);
	}

	/**
	 * Build the map of route components based on the array of path chunks extracted from the route path.
	 * @param array $route_data - The array of chunks extracted from the raw route path string.
	 * @return array - The parsed array of route components that we've hammered out.
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function buildRouteMap(array $route_data)
	{
		// example format:
		// "$var:string"
		// the value for the request component is stored in entry "var", and is typecast as string
		$route_map = array();
		$i = 0;
		foreach($route_data as $slice)
		{
			// Trim out any extra slashes if they exist.
			$slice = trim($slice, '/');

			// Is this a variable component in the route?
			if($slice[0] !== '$')
			{
				$route_map[$i] = array('entry' => $slice, 'type' => 'static');
			}
			else
			{
				// Trim the dollar sign.
				$slice = substr($slice, 1);

				list($var, $type) = array_pad(explode(':', $slice, 2), 2, 'none');

				if($type != 'none' && !$this->getTypeSupported($type))
				{
					throw new \InvalidArgumentException(sprintf('Unsupported route variable type "%1$s" specified', $type));
				}

				$route_map[$i] = array('entry' => $var, 'type' => $type);
			}
			$i++;
		}

		return $route_map;
	}

	/**
	 * Build a regular expression to represent this current route instance based on an array containing the route component map.
	 * @param array $route_map - The route map to use to build the regular expression.
	 * @return string - The regular expression to use for this route instance.
	 *
	 * @throws \LogicException
	 */
	protected function buildRouteRegex($route_map)
	{
		$regex = '#^';

		foreach($route_map as $component)
		{
			switch($component['type'])
			{
				case 'none':
					$regex .= '/([^/]+)';
				break;

				case 'static':
					$regex .= '/' . preg_quote($component['entry'], '#');
				break;

				case 'str':
				case 'string':
					$regex .= '/([a-zA-Z0-9\-_\.\+\~\?\!]+)';
				break;

				case 'int':
				case 'integer':
					$regex .= '/([0-9]+)';
				break;

				case 'float':
					$regex .= '/([0-9\.]+)';
				break;

				default:
					throw new \LogicException(sprintf('Unsupported route type "%1$s" encountered during route regexp creation', $component['type']));
				break;
			}
		}

		$regex .= '/?$#i';

		return $regex;
	}
}
