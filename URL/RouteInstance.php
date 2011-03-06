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
 * OpenFlame Web Framework - Static URL router route instance,
 * 	     A route instance for the static URL router, provides abstraction of request verification.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class RouteInstance
{
	protected $route_map = array();

	protected $route_regexp = '';

	protected $route_base = '';

	protected $serialized_route = '';

	protected $request_data;

	//protected $route_callback;

	private static $supported_types = array(
		'str'		=> true,
		'string'	=> true,
		'float'		=> true,
		'int'		=> true,
		'integer'	=> true,
	);

	public static function newInstance()
	{
		return new static();
	}

	public function getRouteBase()
	{
		return $this->route_base;
	}

	protected function setRouteBase($base)
	{
		// reset the serialized route on any changes to the route...
		$this->setSerializedRoute(NULL);
		$this->route_base = $base;
		return $this;
	}

	public function getRouteMap()
	{
		return $this->route_map;
	}

	protected function setRouteMap(array $map)
	{
		// reset the serialized route on any changes to the route...
		$this->setSerializedRoute(NULL);
		$this->route_map = $map;
		return $this;
	}

	public function getRouteRegexp()
	{
		return $this->route_regexp;
	}

	protected function setRouteRegexp($regexp)
	{
		// reset the serialized route on any changes to the route...
		$this->setSerializedRoute(NULL);
		$this->route_regexp = $regexp;
		return $this;
	}

	public function getSerializedRoute()
	{
		if(empty($this->serialized_route))
		{
			$route_array = array(
				'route_base'	=> $this->getRouteBase(),
				'route_map'		=> $this->getRouteMap(),
				'route_regexp'	=> $this->getRouteRegexp(),
			);

			$this->setSerializedRoute(serialize($route_array));
		}

		return $this->serialized_route;
	}

	protected function setSerializedRoute($route_string)
	{
		$this->serialized_route = $route_string;
		return $this;
	}

	final public function getTypeSupported($type)
	{
		return isset(self::$supported_types[$type]);
	}

	public function loadRawRoute($route)
	{
		$route_data = explode('/', $route, \OpenFlame\Framework\URL\Router::EXPLODE_LIMIT);

		$this->setRouteBase($route_data[0])
			->setRouteMap($this->buildRouteMap($route_data))
			->setRouteRegexp($this->buildRouteRegex());

		return $this;
	}

	public function loadSerializedRoute($route_string)
	{
		$route_data = unserialize($route_string);

		// Protected against bunk data
		if($route_data === false || !isset($route_data['route_base']) || !isset($route_data['route_map']) || !isset($route_data['route_regexp']))
		{
			throw new \RuntimeException('Route unserialization failed, data extracted is invalid or incomplete');
		}

		// Load the route data seamlessly
		$this->setRouteBase($route_data['route_base'])
			->setRouteMap($route_data['route_map'])
			->setRouteRegexp($route_data['route_regexp'])
			->setSerializedRoute($route_string);

		return $this;
	}

	// does the request match this route?
	public function verify($request)
	{
		// array - $request
		// asdf
	}

	// extract data using the regexp from the request
	public function loadRequest($request)
	{
		// array - $request
		// asdf
	}

	// will grab extracted data from the request
	public function getDataPoint($point)
	{
		// asdf
	}

	protected function buildRouteMap(array $route_data)
	{
		// example format:
		// "$var:string"
		// the value for the request component is stored in entry "var", and is typecast as string
		$route_map = array();
		foreach($route_data as $slice)
		{
			// Is this a variable component in the route?
			if($slice[0] !== '$')
			{
				array_push($route_map, array('entry' => $slice, 'type' => 'static'));
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

				array_push($route_map, array('entry' => $var, 'type' => $type));
			}
		}

		return $route_map;
	}

	protected function buildRouteRegex()
	{
		$regex = '#';

		foreach($this->getRouteMap() as $component)
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
					$regex .= '/([a-zA-Z0-9\-_\. ]+)';
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

		$regex .= '/?#i';

		return $regex;
	}
}
