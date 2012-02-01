<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  input
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Input;

/**
 * OpenFlame Framework - Input object handler
 * 	     Handles fluid creation of input objects, the transparent use of field juggling, and provides registration/access to validator callbacks for input instances to use.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class Handler
{
	/**
	 * Get an input instance for a specific input (in format "POST::inputfieldhere")
	 * @param string $name - The name of the input to grab (and the type of input to get, type defaults to REQUEST)
	 *                      Format must be "TYPE::NAME", examples: POST::username, GET::sid, COOKIE::cookiename, etc
	 * @param mixed $default - The default value to set for this input, as a shortcut.
	 * @return \emberlabs\openflame\Input\Instance - An input instance to manipulate, set on fire, etc.
	 */
	public function getInput($name, $default = NULL)
	{
		list($type, $field) = array_pad(explode('::', $name, 2), -2, '');

		$instance = Instance::newInstance()
			->setType($type)
			->setName($field);

		if($default !== NULL)
		{
			$instance->setDefault($default);
		}

		return $instance;
	}
}
