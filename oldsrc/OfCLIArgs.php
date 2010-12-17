<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('OF_ROOT')) exit;

/**
 * OpenFlame Web Framework - CLI arg input class,
 * 		Processes CLI arg input.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfCLIArgs implements ArrayAccess
{
	const VALIDATE_BOOLEAN = 1;
	const VALIDATE_INCREMENT = 2;
	const VALIDATE_VALUE_INT = 3;
	const VALIDATE_VALUE_STRING = 4;
	const VALIDATE_MULTIVALUE_INT = 5;
	const VALIDATE_MULTIVALUE_STRING = 6;
	const VALIDATE_MULTIVALUE_ARRAY = 7;

	/**
	 * @var string - The name of of application that is using us.
	 */
	protected $name = '';

	/**
	 * @var string - The description of the application that is using us.
	 */
	protected $description = '';

	/**
	 * @var string - The version stamp of the application that is using us.
	 */
	protected $version = '';

	/**
	 * @var array - Array of all declared CLI args that we can process.
	 */
	protected $map = array();

	/**
	 * Constructor
	 * @param string $app_name - The name of the app using us.
	 * @param string $app_desc - The description of the app using us.
	 * @param string $app_version - The version of the app using us.
	 * @param array $map - The map of the CLI args we can process, to be processed and interpreted.
	 * @return void
	 */
	final public function __construct($app_name, $app_desc, $app_version, array $map = array())
	{
		// Dump in some properties here regarding what the hell is using us...
		list($this->name, $this->description, $this->version) = array($app_name, $app_desc, $app_version);

		if(!empty($map))
		{
			foreach($map as $name => $options)
				$this->addArg($name, $options);
		}
	}

	/**
	 * Register an arg with the handler.
	 * @param string $arg_name - The name to register this arg as.
	 * @param array $arg_options - The options for this arg.
	 * @return void
	 */
	final public function addArg($arg_name, array $arg_options)
	{
		$this->map[$arg_name] = new OfCLIArgMap($arg_name, $arg_options);
	}

	/**
	 * Parse an array of CLI arguments against declared CLI args.
	 * @param array $args - The array of CLI args to parse (just an exploded version of $argv is needed)
	 * @return void
	 */
	final public function parseArgs(array $args)
	{
		// Make sure we don't have an extra arg in here...
		$first_arg = array_shift($args);
		if($first_arg !== basename(__FILE__))
			array_unshift($args, $first_arg);
		unset($first_arg);

		$last_map = NULL;
		/* @var $last_map OfCLIArgMap */
		foreach($args as $arg)
		{
			if($arg[0] == '-')
			{
				/* @var $map OfCLIArgMap */
				foreach($this->map as $map_name => $map)
				{
					if(!$map->handlesArg($arg))
						continue;

					switch($map->getProperty('validate'))
					{
						case self::VALIDATE_BOOLEAN:
							$map->addValue(true);
						break;

						case self::VALIDATE_INCREMENT:
							$last_map->addValue(1);
						break;
					}

					// Set $last_map, which holds the last argmap encountered.
					$last_map = $map;
				}
			}
			else
			{
				if(is_null($last_map))
					continue;

				// Using a switch here as we may want to expand upon this later.
				switch($last_map->getProperty('validate'))
				{
					case self::VALIDATE_VALUE_INT:
					case self::VALIDATE_VALUE_STRING:
					case self::VALIDATE_MULTIVALUE_INT:
					case self::VALIDATE_MULTIVALUE_STRING:
					case self::VALIDATE_MULTIVALUE_ARRAY:
						$last_map->addValue($arg);
					break;
				}
			}
		}
	}

	/**
	 * asdf
	 */
	public function buildHelp()
	{
		// asdf
	}

	/**
	 * asdf
	 */
	public function buildVersion()
	{
		// asdf
	}

	/**
	 * ArrayAccess methods
	 * @todo recode most of this
	 */

	/**
	 * Check if an "array" offset exists in this object.
	 * @param mixed $offset - The offset to check.
	 * @return boolean - Does anything exist for this offset?
	 */
	public function offsetExists($offset)
	{
		return isset($this->map[$offset]);
	}

	/**
	 * Get an "array" offset for this object.
	 * @param mixed $offset - The offset to grab from.
	 * @return mixed - The value of the offset, or null if the offset does not exist.
	 */
	public function offsetGet($offset)
	{
		return (isset($this->map[$offset])) ? $this->map[$offset] : NULL;
	}

	/**
	 * Set an "array" offset to a certain value, if the offset exists
	 * @param mixed $offset - The offset to set.
	 * @param mixed $value - The value to set to the offset.
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->map[$offset]->addValue($value);
	}

	/**
	 * Unset an "array" offset.
	 * @param mixed $offset - The offset to clear out.
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->map[$offset]);
	}
}

/**
 * OpenFlame Web Framework - CLI arg map object,
 * 		Provides the structure for the CLI arg objects to fill, and handles things like data validation.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfCLIArgMap
{
	const VALIDATE_BOOLEAN = 1;
	const VALIDATE_INCREMENT = 2;
	const VALIDATE_VALUE_INT = 3;
	const VALIDATE_VALUE_STRING = 4;
	const VALIDATE_MULTIVALUE_INT = 5;
	const VALIDATE_MULTIVALUE_STRING = 6;
	const VALIDATE_MULTIVALUE_ARRAY = 7;

	// @todo document me
	protected $map_name = '';

	protected $long_names = array();
	protected $short_names = array();
	protected $arg_names = array();

	protected $validate = self::VALIDATE_BOOLEAN;
	protected $default_value;
	protected $type = '';

	protected $description = '';

	protected $value;

	protected $options = array();

	/**
	 * Constructor
	 * @param string - The name assigned to this arg map object instance.
	 * @param array $data - The data to dump into the arg.
	 * @return void
	 */
	public function __construct($map_name, array $data)
	{
		$this->map_name = $map_name;

		if(isset($data['short_names']))
		{
			// Force all short_names to start with the usual dash
			foreach($data['short_names'] as $key => $name)
			{
				if(substr($name, 0, 1) != '-')
					$data['short_names'][$key] = "-$name";
			}
			$this->short_names = (array) $data['short_names'];
		}

		if(isset($data['long_names']))
		{
			// Force all long_names to start with the usual double-dash
			foreach($data['long_names'] as $key => $name)
			{
				if(substr($name, 0, 2) != '--')
					$data['long_names'][$key] = "--$name";
			}
			$this->long_names = (array) $data['long_names'];
		}

		$this->arg_names = array_merge($this->short_names, $this->long_names);

		if(isset($data['options']))
			$this->options = (array) $data['options'];

		if(isset($data['default']))
		{
			switch($data['validate'])
			{
				case self::VALIDATE_BOOLEAN:
					$this->default_value = (bool) $data['default'];
				break;

				case self::VALIDATE_VALUE_INT:
				case self::VALIDATE_INCREMENT:
					$this->default_value = (int) $data['default'];
				break;

				case self::VALIDATE_VALUE_STRING:
					$this->default_value = (string) $data['default'];
				break;

				case self::VALIDATE_MULTIVALUE_INT:
					// If it isn't an array already, we just typecast to int and stuff it in an array.
					if(!is_array($data['default']))
					{
						$this->default_value = array((int) $data['default']);
					}
					else
					{
						// Typecast everything inside to int.
						array_walk($data['default'], 'intval');
						$this->default_value = $data['default'];
					}
				break;

				case self::VALIDATE_MULTIVALUE_STRING:
					// If it isn't an array already, we just typecast to string and stuff it in an array.
					if(!is_array($data['default']))
					{
						$this->default_value = array((string) $data['default']);
					}
					else
					{
						// Typecast everything inside to string.
						array_walk($data['default'], 'strval');
						$this->default_value = $data['default'];
					}
				break;

				case self::VALIDATE_MULTIVALUE_ARRAY:
					// If we aren't an array, we stuff an empty array in here.
					// Why?  Because all array elements in this are supposed to have a user-specified key.
					if(!is_array($data['default']))
						$this->default_value = array();
				break;

				default:
					$this->default_value = false;
					$data['validate'] = self::VALIDATE_BOOLEAN;
				break;
			}
		}

		$this->description = $data['description'];
		$this->validate = $data['validate'];

		if(!is_null($this->default_value) || !isset($this->options['typecast.disable']) || !$this->options['typecast.disable'])
			$this->type = gettype($data['default']);
	}

	/**
	 * Get the value of an internal property.
	 * @param string $property - The name of the internal property to grab data for.
	 * @return mixed - The value of the property if it exists, or NULL if no such property.
	 */
	public function getProperty($property)
	{
		if(!property_exists($this, $property))
			return NULL;
		return $this->$property;
	}

	/**
	 * Check to see if we handle a certain argument or not.
	 * @param string $arg_name - The argument to check.
	 * @return boolean - Do we handle this argument or not?
	 */
	public function handlesArg($arg_name)
	{
		return in_array($arg_name, $this->arg_names);
	}

	/**
	 * Adds another value to the current value set.
	 * @param mixed $value - The value to add in.
	 * @return boolean - True on success, false on failure.
	 */
	public function addValue($value)
	{
		switch($this->validate)
		{
			case self::VALIDATE_BOOLEAN:
				$this->value = $value;
			break;

			case self::VALIDATE_INCREMENT:
				$value = (int) $value;
				for($i = 1; $i <= $value; $i++)
				{
					$this->value++;
				}
			break;

			case self::VALIDATE_VALUE_INT:
				$this->value += $value;
			break;

			case self::VALIDATE_VALUE_STRING:
				$this->value = trim($this->value . " $value");
			break;

			case self::VALIDATE_MULTIVALUE_INT:
				$this->value[] = (int) $value;
			break;

			case self::VALIDATE_MULTIVALUE_STRING:
				$this->value[] = (string) $value;
			break;

			case self::VALIDATE_MULTIVALUE_ARRAY:
				$value_exp = explode('=', $value);
				$key = array_shift($value_exp);
				$this->value[$key] = (string) $value_exp;
			break;
		}
	}
}
