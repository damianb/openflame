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
use OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - User Input Handler
 * 	     Allows for safe user input and validation of such.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Instance
{
	/**
	 * @var \OpenFlame\Framework\Input\Handler - The input handler.
	 */
	protected $handler;

	/**
	 * @var mixed - The raw input
	 */
	protected $raw_value = NULL;

	/**
	 * @var mixed - The cleaned input
	 */
	protected $clean_value = NULL;

	/**
	 * @var boolean - Has this value even been set?
	 */
	protected $was_set = false;

	/**
	 * @var string - The superglobal to grab this input from.
	 */
	protected $global_type = '_REQUEST';

	/**
	 * @var string - The field name we're grabbing from.
	 */
	protected $field_name = '';

	/**
	 * @var sting - The "juggled" field name we're grabbing from, if we are using field juggling.
	 */
	protected $juggled_name = '';

	/**
	 * @var mixed - The default value to use for this instance (also validates/binds the format of the input to match the default value).
	 */
	protected $default_value;

	/**
	 * @var boolean - Do we want to use field juggling on this input?
	 */
	protected $use_juggling = true;

	/**
	 * @var boolean - Has this input instance been processed since it had its properties last set?
	 */
	protected $processed = false;

	/**
	 * Get a new input instance.
	 * @return \OpenFlame\Framework\Input\Instance - The newly created input instance.
	 */
	final public static function newInstance()
	{
		return new static();
	}

	/**
	 * Link the input handler to this input instance
	 * @param \OpenFlame\Framework\Input\Handler $handler - The input handler.
	 * @return \OpenFlame\Framework\Input\Instance - Provides a fluent interface.
	 */
	public function setHandler(\OpenFlame\Framework\Input\Handler $handler)
	{
		$this->handler = $handler;
		return $this;
	}

	/**
	 * Get the superglobal to grab the input from.
	 * @return string - The superglobal we're grabbing from.
	 */
	public function getType()
	{
		return $this->global_type;
	}

	/**
	 * Set the superglobal to grab the input from.
	 * @param string $global_type - The superglobal to grab from.
	 * @return \OpenFlame\Framework\Input\Instance - Provides a fluent interface.
	 */
	public function setType($global_type)
	{
		$global_type = '_' . ltrim($global_type, '_');
		if(!in_array($global_type, array('_REQUEST', '_GET', '_POST', '_COOKIE', '_SERVER', '_FILES')))
			$global_type = '_REQUEST';

		$this->wipeInstance();
		$this->global_type = $global_type;
		return $this;
	}

	/**
	 * Get the default value for this input instance.
	 * @return mixed - The default value for this input instance.
	 */
	public function getDefault()
	{
		return $this->default_value;
	}

	/**
	 * Set the default value for this input instance.
	 * @param mixed - The default value to use for this input instance.
	 * @return \OpenFlame\Framework\Input\Instance - Provides a fluent interface.
	 */
	public function setDefault($default_value)
	{
		$this->wipeInstance();
		$this->default_value = $default_value;
		return $this;
	}

	/**
	 * Get the field name that this instance is attached to.
	 * @return string - The field name for this instance.
	 */
	public function getName()
	{
		return $this->field_name;
	}

	/**
	 * Set the field name that we want to attach this instance to.
	 * @param string $name - The field name to use for this instance.
	 * @return \OpenFlame\Framework\Input\Instance - Provides a fluent interface.
	 */
	public function setName($name)
	{
		$this->wipeInstance();
		$this->field_name = $name;
		return $this;
	}

	/**
	 * Get the juggled field name that this instance is attached to.
	 * @return string - The juggled field name for this instance.
	 */
	public function getJuggledName()
	{
		return $this->juggled_name;
	}

	/**
	 * Set the juggled field name that we want to attach this instance to.
	 * @param string $name - The juggled field name to use for this instance.
	 * @return \OpenFlame\Framework\Input\Instance - Provides a fluent interface.
	 */
	public function setJuggledName($name)
	{
		$this->wipeInstance();
		$this->juggled_name = $name;
		return $this;
	}

	/**
	 * Set this instance to use field juggling (note, field juggling defaults to being enabled)
	 * @return \OpenFlame\Framework\Input\Instance - Provides a fluent interface.
	 */
	public function enableFieldJuggling()
	{
		$this->wipeInstance();
		$this->use_juggling = true;
		return $this;
	}

	/**
	 * Set this instance to not use field juggling (note, field juggling defaults to being enabled)
	 * @return \OpenFlame\Framework\Input\Instance - Provides a fluent interface.
	 */
	public function disableFieldJuggling()
	{
		$this->wipeInstance();
		$this->use_juggling = false;
		return $this;
	}

	/**
	 * Check to see if this input instance is to use field juggling
	 * @return boolean - Do we want to use field juggling?
	 */
	public function useJuggling()
	{
		return $this->use_juggling;
	}

	/**
	 * Get the sanitized (not escaped, though!) value for this input's var
	 * @return mixed - The cleaned value of the var.
	 */
	public function getClean()
	{
		if(!$this->processed)
			$this->processVar();
		return $this->clean_value;
	}

	/**
	 * Get the raw, unprocessed value for this input's var
	 * @return mixed - The raw value for the var.
	 */
	public function getRaw()
	{
		if(!$this->processed)
			$this->processVar();
		return $this->raw_value;
	}

	/**
	 * Check to see if the input var this instance represents was set or not
	 * @return boolean - Was the var set?
	 */
	public function getWasSet()
	{
		if(!$this->processed)
			$this->processVar();
		return $this->was_set;
	}

	/**
	 * Recursively digs through the default to ensure everything is in it's place.
	 * @param mixed $var
	 * @param mixed $default
	 * @return mixed The cleaned data.
	 */
	protected function cleanVar($var, $default)
	{
		if(is_array($var))
		{
			list($_key_default, $_value_default) = each($default);

			foreach($var as $key => $value)
				$var[$this->bindVar($key, $_key_default)] = $this->cleanVar($value, $_value_default);
		}
		else
		{
			$this->bindVar($var, $default);
		}

		return $var;
	}

	/**
	 * Binds the var to its final type
	 * @param mixed &$var - The var being bound
	 * @param mixed $default - Default value
	 * @return void
	 */
	protected function bindVar(&$var, $default)
	{
		$type = gettype($default);
		settype($var, $type);

		if($type == 'string')
		{
			if(!mb_check_encoding($var))
				$var = $default;

			$var = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $var), ENT_COMPAT, 'UTF-8'));
		}
	}

	/**
	 * Internal method that processes the desired input var and loads its data (and sanitizes it)
	 * @return void
	 *
	 * @throws \LogicException
	 */
	protected function processVar()
	{
		if($this->processed)
			return;

		if($this->getName() === NULL)
			throw new \LogicException('No field name specified for input retrieval in \\OpenFlame\\Framework\\Input\\Instance');

		if($this->getDefault() === NULL)
			throw new \LogicException('Cannot specify NULL as the default value for input in \\OpenFlame\\Framework\\Input\\Instance');

		// Check to see if we want to override the field name juggling feature
		if($this->useJuggling())
		{
			$name = $this->getName();
		}
		else
		{
			$name = $this->getJuggledName() ?: $this->getName();
		}

		if($this->getType() == '_REQUEST' && isset($_COOKIE[$this->getName()]))
			$_REQUEST[$this->getName()] = isset($_POST[$name]) ?: $_GET[$name];

		$this->was_set = (!empty($GLOBALS[$this->getType()][$name])) ? true : false;

		$this->raw_value = ($this->was_set) ? $GLOBALS[$this->getType()][$name] : $this->getDefault();
		$this->clean_value = $this->cleanVar($this->raw_value, $this->getDefault());

		// Flag this instance as having been processed, so that we don't re-process the same data again.
		$this->processed = true;
	}

	/**
	 * Someone changed some of the internal data for this instance, so we need to wipe any raw/clean data if we've already been processed, just in case.
	 * @return void
	 */
	protected function wipeInstance()
	{
		if(!$this->processed)
			return;

		$this->raw_value = $this->clean_value = NULL;
		$this->processed = $this->was_set = false;
	}

	/**
	 * Validate the contained data using a validator function registered in the input handler
	 * @param string $type - The type of validator to use
	 * @return boolean - Does the input validate?
	 *
	 * @throws \LogicException
	 * @throws \RuntimeException
	 */
	protected function validate($type)
	{
		if(empty($this->handler))
		{
			throw new \LogicException('Cannot validate input instance as input handler has not been linked to the instance');
		}

		$validator = $this->handler->getValidator($type);
		if($validator === false)
		{
			throw new \RuntimeException(sprintf('No validator registered for validation type "%1$s" in \\OpenFlame\\Framework\\Input\\Instance', $type));
		}

		return (bool) call_user_func($validator, $this->getClean());
	}

	/**
	 * Shortcut for getting the sanitized value for this input's var
	 * @return mixed - The cleaned value of the var.
	 */
	public function __toString()
	{
		if(!$this->processed)
			$this->processVar();
		return $this->clean_value;
	}
}
