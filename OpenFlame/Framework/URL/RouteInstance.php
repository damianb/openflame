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
	protected $components = array();

	private $supported_types = array(
		'str'		=> true,
		'string'	=> true,
		'float'		=> true,
		'int'		=> true,
		'integer'	=> true,
	);

	public function __construct($route)
	{
		$route_data = explode('/', $route, \OpenFlame\Framework\URL\Router::EXPLODE_LIMIT);

		// ex format:
		// $var:string
		// the value for the request component is stored in "$var", and is typecast as string
		foreach($route_data as $slice)
		{
			// Is this a variable component in the route?
			if(!strpos($slice, '$'))
			{
				array_push($this->components[$i], array('value' => $slice, 'type' => 'static'));
			}
			else
			{
				// Trim the dollar sign.
				$slice = substr($slice, 1);

				list($var, $type) = array_pad(explode(':', $slice, 2), 2, '');
				if($type === '')
					$type = 'none';

				if($type != 'none' && !isset($this->supported_types[$type]))
					throw new \Exception(); // @todo exception

				array_push($this->components, array('value' => $var, 'type' => $type));
			}
		}
	}

	protected function buildRegex()
	{
		$regex = '#';

		foreach($this->components as $component)
		{
			switch($component['type'])
			{
				case 'static':
					$regex .= '/' . preg_quote($component['value'], '#');
				break;

				case 'none':
					$regex .= '/([^/]+)';
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
			}
		}

		$regex .= '/?#i';

		return $regex;
	}
}
