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

namespace OpenFlame\Framework\Input;

if(!defined('OpenFlame\\Framework\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Some class
 * 	     Some class description.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Handler
{
	/**
	 * @var boolean - Do we want to enable field juggling for newly created input instances?
	 */
	protected $enable_field_juggling = false;

	/**
	 * @var string - A unique string specific to an individual user's session
	 */
	protected $session_juggle_salt = '';

	/**
	 * @var string - A unique string specific to an individual installation
	 */
	protected $global_juggle_salt = '';

	/**
	 * Get an input instance for a specific input (in format "POST::inputfieldhere")
	 * @note this method will automatically and transparently handle field juggling itself
	 *      To disable field juggling per individual instance, use $instance->disableFieldJuggling();
	 * @param string $name - The name of the input to grab (and the type of input to get, type defaults to REQUEST)
	 *                      Format must be "TYPE::NAME", examples: POST::username, GET::sid, COOKIE::cookiename, etc
	 * @return \OpenFlame\Framework\Input\Instance - An input instance to manipulate, set on fire, etc.
	 */
	public function getInput($name)
	{
		list($type, $field) = array_pad(explode('::', $name, 2), -2, '');

		$instance = \OpenFlame\Framework\Input\Instance::newInstance()->setType($type)->setName($field);
		if($this->useJuggling())
			$instance->setJuggledName($this->buildJuggledName($field));

		return $instance;
	}

	public function buildJuggledName($name)
	{
		return $name . '_' . hash('md5', $name . $this->getSessionJuggleSalt() . $this->getGlobalJuggleSalt());
	}

	public function getSessionJuggleSalt()
	{
		return $this->session_juggle_salt;
	}

	public function setSessionJuggleSalt($salt)
	{
		$this->session_juggle_salt = $salt;
		return $this;
	}

	public function getGlobalJuggleSalt()
	{
		return $this->global_juggle_salt;
	}

	public function setGlobalJuggleSalt($salt)
	{
		$this->global_juggle_salt = $salt;
		return $this;
	}

	public function enableFieldJuggling()
	{
		$this->enable_field_juggling = true;
		return $this;
	}

	public function disableFieldJuggling()
	{
		$this->enable_field_juggling = false;
		return $this;
	}

	public function useJuggling()
	{
		return $this->enable_field_juggling;
	}

	public function getValidator($type)
	{
		if(!isset($this->validators[$type]))
			return false;
		return $this->validators[$type];
	}

	public function registerValidator($type, $callback)
	{
		// asdf
	}
}
