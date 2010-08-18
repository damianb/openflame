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
 */
class Of
{
	// @todo phpdocs
	protected $config = array();
	protected $objects = array();

	public static function loadConfigFile($filename)
	{
		// load the config filename
		// @todo use json file?  use php file and include?
	}

	public static function config($config_name)
	{
		// pull the config we want
	}

	public static function loader($class)
	{
		// code to autoload a file based on class name
	}

	public static function storeObject($slot, $object)
	{
		// store one of the OpenFlame Framework objects...
	}

	public static function getObject($slot)
	{
		// get one of the OpenFlame Framework objects...
	}

	// alias of Of::getObject()
	public static function obj($slot)
	{
		self::getObject($slot);
	}
}
