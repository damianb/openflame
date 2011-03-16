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

namespace OpenFlame\Framework;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Main class
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
	 * @var array - Array of settings we have loaded and stored
	 */
	protected static $config = array();

	/**
	 * @var array - Array of objects we have instantiated and stored
	 */
	protected static $objects = array();

	/**
	 * Initiates the Framework.
	 * @param array $settings - Array of application-specific settings to modify the default OpenFlame Framework behavior.
	 * @return void
	 */
	public static function init(array $settings = NULL)
	{
		// Set our defaults
		$config = array(
			'cache.engine'		=> 'JSON',
			'cache.path'		=> OpenFlame\ROOT_PATH . '/cache/',
		);

		// Merge in the application-specific settings.
		if($properties !== NULL)
			$config = array_merge($config, $properties);

		// Yay lambdas!
		array_walk($config, function($value, $key) {
			self::setConfig($key, $value);
		});
	}

	/**
	 * Set a configuration entry.
	 * @param string $config_name - The name of the configuration entry.
	 * @param mixed $config_value - The value to store in the configuration entry
	 * @return void
	 */
	public static function setConfig($config_name, $config_value)
	{
		// check to see if this is a namespaced config
		$config_name = explode('.', $config_name, 2);
		if(sizeof($config_name) > 1)
		{
			// it is namespaced, we need to store under said namespace
			self::$config["_{$config_name[0]}"][$config_name[1]] = $config_value;
		}
		else
		{
			// if no namespace was declared, we store it in the global namespace
			self::$config['global'][$config_name[0]] = $config_value;
		}
	}

	/**
	 * Get a specific configuration entry
	 * @param string $config_name - The name of the configuration entry to grab.
	 * @return mixed - The contents of the specified configuration entry.
	 */
	public static function getConfig($config_name)
	{
		// check to see if this is a namespaced config
		$config_name_array = explode('.', $config_name, 2);
		if(sizeof($config_name_array) > 1)
		{
			// it is namespaced, we need to grab from that specific namespace.
			if(!isset(self::$config["_{$config_name_array[0]}"][$config_name]))
				return NULL;

			return self::$config["_{$config_name_array[0]}"][$config_name];
		}
		else
		{
			// not namespaced, so we use the global namespace for this. :)
			if(!isset(self::$config['global'][$config_name]))
				return NULL;

			return self::$config['global'][$config_name];
		}
	}

	/**
	 * Get all configurations under a certain namespace.
	 * @param string $namespace - The namespace to retrieve (or an empty string, to retrieve the global config namespace contents)
	 * @return array - The array of configurations stored under the specified namespace.
	 */
	public static function getConfigNamespace($namespace)
	{
		// If an empty string is used as the namespace, we assume the global namespace.
		if($namespace === '')
		{
			if(!isset(self::$config['global']))
				return NULL;

			return self::$config['global'];
		}

		if(!isset(self::$config["_{$namespace}"]))
			return NULL;

		return self::$config["_{$namespace}"];
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
			return NULL;

		return self::$objects[(string) $slot];
	}
}
