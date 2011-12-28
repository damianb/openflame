<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  input
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Input;

/**
 * OpenFlame Framework - Input object handler
 * 	     Handles fluid creation of input objects, the transparent use of field juggling, and provides registration/access to validator callbacks for input instances to use.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Juggler extends Handler
{
	/**
	 * @var string - A unique string specific to an individual user's session
	 */
	protected $session_juggle_salt = '';

	/**
	 * @var string - A unique string specific to an individual installation
	 */
	protected $global_juggle_salt = '';

	/**
	 * @var array - Array of juggled field names and their hashes, to allow for increased speed retrieving juggled names
	 */
	protected $juggle_hash_cache = array();

	/**
	 * Get an input instance for a specific input (in format "POST::inputfieldhere")
	 * @param string $name - The name of the input to grab (and the type of input to get, type defaults to REQUEST)
	 *                      Format must be "TYPE::NAME", examples: POST::username, GET::sid, COOKIE::cookiename, etc
	 * @param mixed $default - The default value to set for this input, as a shortcut.
	 * @return \OpenFlame\Framework\Input\Instance - An input instance to manipulate, set on fire, etc.
	 */
	public function getInput($name, $default = NULL)
	{
		list($type, $field) = array_pad(explode('::', $name, 2), -2, '');

		$instance = Instance::newInstance()
			->setType($type)
			->setName($this->buildJuggledName($field));

		if($default !== NULL)
		{
			$instance->setDefault($default);
		}

		return $instance;
	}

	/**
	 * Builds the juggled field name to use for an input
	 * @param string $name - The "pure" field name to use as the base for the field juggling
	 * @return string - The juggled field name.
	 *
	 * @note Hopefully this will help strengthen the input system against automated submissions and CSRF.  We'll see.
	 */
	public function buildJuggledName($name)
	{
		if(isset($this->juggle_hash_cache[$name]))
		{
			return $this->juggle_hash_cache[$name];
		}

		$hash = $name[0] . '_' . hash('md5', $name . $this->getSessionJuggleSalt() . $this->getGlobalJuggleSalt());
		$this->juggle_hash_cache[$name] = $hash;

		return $hash;
	}

	/**
	 * Get the session-specific salt that we're using for generating juggled field names.
	 * @return string - The session salt for juggling.
	 */
	public function getSessionJuggleSalt()
	{
		return $this->session_juggle_salt;
	}

	/**
	 * Set the session-specific salt that we're using for generating juggled field names.
	 * @param string $salt - The session salt for juggling.
	 * @return \OpenFlame\Framework\Input\Juggler - Provides a fluent interface.
	 */
	public function setSessionJuggleSalt($salt)
	{
		$this->juggle_hash_cache = array(); // reset the hash cache as all juggled field names cached are no longer valid
		$this->session_juggle_salt = $salt;

		return $this;
	}

	/**
	 * Get the installation-specific salt that we're using for generating juggled field names.
	 * @return string - The installation salt for juggling.
	 */
	public function getGlobalJuggleSalt()
	{
		return $this->global_juggle_salt;
	}

	/**
	 * Set the installation-specific salt that we're using for generating juggled field names.
	 * @param string $salt - The installation salt for juggling.
	 * @return \OpenFlame\Framework\Input\Juggler - Provides a fluent interface.
	 */
	public function setGlobalJuggleSalt($salt)
	{
		$this->juggle_hash_cache = array(); // reset the hash cache as all juggled field names cached are no longer valid
		$this->global_juggle_salt = $salt;

		return $this;
	}
}
