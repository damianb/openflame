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
 * OpenFlame Web Framework - Main class
 * 	     Contains the static objects that power the framework
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note        This class should not be instantiated; it should only be statically accessed.
 */
class Of
{
	/**
	 * @var array - Array of settings we have loaded and stored
	 */
	protected static $config = array();

	/**
	 * @var array - Array of objects we have instantiated and stored
	 */
	protected static $objects = array();

	/**
	 * Loads an array of settings into the config array.
	 * @param array $config_ary - The array of settings to load
	 * @return void
	 */
	public static function loadConfig(array $config_ary)
	{
		// note: this method can easily be used to load from a JSON config file as well!
		// Of::loadConfig(OfJSON::decode('./path_to_json/config.json'));
		self::$config = array_merge(self::$config, $config_ary);
	}

	/**
	 * Grab a setting from the config array
	 * @param string $config_name - The name of the setting to retrieve
	 * @return mixed - The setting's value if the setting exists, or NULL if the setting does not exist
	 */
	public static function config($config_name)
	{
		if(!isset(self::$config[$config_name]))
			return NULL;
		return self::$config[$config_name];
	}

	/**
	 * Provides autoloader functionality for the OpenFlame Framework
	 * @param string $class - The class to load up
	 * @return boolean - True if load successful, false if we could not find the file to load
	 *
	 * @throws Exception
	 *
	 * @note - We use exception here, instead of OfException, to make the class "Of" standalone
	 */
	public static function loader($class)
	{
		if(file_exists(OF_ROOT . basename($class) . '.php'))
		{
			require OF_ROOT . basename($class) . '.php';
			if(!class_exists($class, false))
				throw new Exception('OpenFlame Framework Autoloader failed to load correct class file for class "' . $class . '"');
			return true;
		}
		return false;
	}

	/**
	 * Store an object for easy global access.
	 * @param string $slot - The slot to store in.
	 * @param object $object - The object to store.
	 * @return void
	 */
	public static function storeObject($slot, $object)
	{
		self::$config[(string) $slot] = $object;
	}

	/**
	 * Grab a stored object.
	 * @param string $slot - The slot to grab from.
	 * @return mixed - NULL if no object in specified slot, or the desired object if the slot exists.
	 */
	public static function getObject($slot)
	{
		if(!isset(self::$config[$slot]))
			return NULL;
		return self::$config[$slot];
	}

	/**
	 * Alias of self::getObject() for quick coding purposes
	 * @see Of::getObject()
	 */
	public static function obj($slot)
	{
		self::getObject($slot);
	}
}
