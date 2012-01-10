<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  core
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Main class
 * 	     Contains the objects that power the framework.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note        This class should not be instantiated; it should only be statically accessed.
 */
class Core
{
	/**
	 * DO NOT _EVER_ CHANGE THESE, FOR THE SAKE OF HUMANITY.
	 * @link http://xkcd.com/534/
	 */
	const CAN_BECOME_SKYNET = false;
	const COST_TO_BECOME_SKYNET = 999999999999;

	/**
	 * @var string - The version for the Framework
	 */
	private static $version = '2.0.0-rc2';

	/**
	 * @var array - Array of settings we have loaded and stored
	 */
	protected static $config = array();

	/**
	 * @var array - Array of objects we have instantiated and stored
	 */
	protected static $objects = array();

	/**
	 * Initiates the Framework.
	 * @param array $config - Array of application-specific settings to store in the OpenFlame Framework core.
	 * @return void
	 */
	public static function init(array $config = NULL)
	{
		if($config !== NULL)
		{
			// Yay lambdas!
			array_walk($config, function($value, $key) {
				self::setConfig($key, $value);
			});
		}
	}

	/**
	 * Get the version string for the current instance of the OpenFlame Framework
	 * @return string - The framework's version.
	 */
	public static function getVersion()
	{
		return self::$version;
	}

	/**
	 * Set a configuration entry.
	 * @param string $config_name - The name of the configuration entry.
	 * @param mixed $config_value - The value to store in the configuration entry
	 * @return void
	 */
	public static function setConfig($config_name, $config_value)
	{
		self::$config[$config_name] = $config_value;
	}

	/**
	 * Get a specific configuration entry
	 * @param string $config_name - The name of the configuration entry to grab.
	 * @return mixed - The contents of the specified configuration entry.
	 */
	public static function getConfig($config_name)
	{
		return self::$config[$config_name];
	}

	/**
	 * Store an object for easy global access.
	 * @param string $slot - The slot to store in.
	 * @param object $object - The object to store.
	 * @return void
	 */
	public static function setObject($slot, $object)
	{
		self::$objects[(string) $slot] = $object;

		return $object;
	}

	/**
	 * Grab a stored object.
	 * @param string $slot - The slot to grab from.
	 * @return mixed - NULL if no object in specified slot, or the desired object if the slot exists.
	 */
	public static function getObject($slot)
	{
		if(!isset(self::$objects[(string) $slot]))
		{
			return NULL;
		}

		return self::$objects[(string) $slot];
	}
}
