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

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - CLI arg input class,
 * 		Processes CLI arg input.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfCLIArgs implements ArrayAccess
{
	const VALIDATE_NONE = 0;
	const VALIDATE_BOOLEAN = 1;
	const VALIDATE_INCREMENT = 2;
	const VALIDATE_VALUE_INT = 3;
	const VALIDATE_VALUE_STRING = 4;
	const VALIDATE_MULTIVALUE_INT = 5;
	const VALIDATE_MULTIVALUE_STRING = 6;
	const VALIDATE_MULTIVALUE_ARRAY = 7;

	protected $name = '';
	protected $description = '';
	protected $version = '';

	/**
	 * @var array - Array of all declared CLI args that we can process.
	 */
	protected $map = array();

	/**
	 * Constructor
	 * @param array $map - The map of the CLI args we can process, to be processed and interpreted.
	 * @return void
	 */
	public function __construct($app_name, $app_desc, $app_version, array $map = array())
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
	 */
	public function addArg($arg_name, $arg_options)
	{
		$this->map[$name] = new OfCLIArgMap($arg_options);
	}

	/**
	 * Blah blah blah...
	 */
	public function parseArgs($args)
	{
		// Make sure we don't have an extra arg in here...
		$first_arg = array_shift($args);
		if($first_arg !== basename(__FILE__))
			array_unshift($args, $first_arg);

		$last_map = NULL;
		foreach($args as $arg)
		{
			if($arg[0] == '-')
			{
				foreach($this->map as $map_name => $map)
				{
					if(!$map->handlesArg($arg))
						continue;

					// @todo process me
				}
			}
			else
			{
				if(is_null($last_map))
					continue;

				$last_map->addValue($arg);
			}
			// asdf
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
		return $this->issetVar($offset);
	}

	/**
	 * Get an "array" offset for this object.
	 * @param mixed $offset - The offset to grab from.
	 * @return mixed - The value of the offset, or null if the offset does not exist.
	 */
	public function offsetGet($offset)
	{
		return ($this->issetVar($offset)) ? $this->fetchVar($offset) : NULL;
	}

	/**
	 * Set an "array" offset to a certain value, if the offset exists
	 * @param mixed $offset - The offset to set.
	 * @param mixed $value - The value to set to the offset.
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->assignVar($offset, $value);
	}

	/**
	 * Unset an "array" offset.
	 * @param mixed $offset - The offset to clear out.
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[(string) $offset]);
	}
}

class OfCLIArgMap
{
	const VALIDATE_NONE = 0;
	const VALIDATE_BOOLEAN = 1;
	const VALIDATE_INCREMENT = 2;
	const VALIDATE_VALUE_INT = 3;
	const VALIDATE_VALUE_STRING = 4;
	const VALIDATE_MULTIVALUE_INT = 5;
	const VALIDATE_MULTIVALUE_STRING = 6;
	const VALIDATE_MULTIVALUE_ARRAY = 7;

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

	public function __construct(array $data)
	{
		$this->map_name = $data['name'];

		// @todo force dashing prefixes on long and short names

		if(isset($data['long_names']))
			$this->long_names = (array) $data['long_names'];

		if(isset($data['short_names']))
			$this->short_names = (array) $data['short_names'];

		$this->arg_names = array_merge($this->long_names, $this->short_names);

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

		$this->validate = $data['validate'];

		if(!is_null($this->default_value) || !isset($this->options['typecast.disable']) || !$this->options['typecast.disable'])
			$this->type = gettype($data['default']);
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
		if(is_array($this->value))
		{
			$this->value = array_merge($this->value, $value);
		}
		elseif(is_int($this->value))
		{
			$this->value += $value;
		}
		elseif(is_string($this->value))
		{
			$this->value = trim($this->value . " $value");
		}
		else
		{
			return false;
		}
		return true;
	}
}
