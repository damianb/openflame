<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 *
 * @uses Doctrine 1.2
 * @uses OfDb.php
 */

if(!defined('OF_ROOT')) exit;

/**
 * OpenFlame Web Framework - Application level configuration manager
 * 	     Allows for easy managerment of configuration key and value pairs
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfConfig implements ArrayAccess
{
	/**
	 * @var $configVals
	 *
	 * Array of values to call from when retrieving configs
	 */
	private $configVals = array();

	/**
	 * @var $tableName
	 *
	 * Name of the table
	 */
	private $tableName = '';

	/**
	 * Constructor
	 *
	 * @var string $tableName The table name of the config table (keeps it independent of DB constants)
	 */
	public function __construct($tableName)
	{
		// Store this for later use
		$this->tableName = $tableName;

		// Grab the values
		$query = Doctrine_Query::create()
			->from("{$tableName} c");
		$rawConfig = $query->fetchArray();

		// Throw them in an array
		foreach($rawConfig as $name => $value)
			$this->configVals[$name] = is_numeric($value) ? (int) $value : (string) $value;

		return;
	}

	/**
	 * Get the offset
	 * Part of ArrayAccess
	 *
	 * @param mixed offset
	 * @return mixed 
	 */
	public function offsetGet($configName)
	{
		$this->configVals[$configName] = is_numeric($this->configVals[$configName]) ? (int) $this->configVals[$configName] : false;

		return !empty($this->configVals[$configName]) ? $this->configVals[$configName] : null;
	}

	/**
	 * Check if the offset exists
	 * Part of ArrayAccess
	 *
	 * @param mixed offset
	 * @return bool
	 */
	public function offsetExists($configName)
	{
		return isset($this->configVals[$configName]) ? true : false;
	}

	/**
	 * Unset the offset
	 * This actually does nothing, we don't want people randomly deleteing stuff
	 * Part of ArrayAccess
	 *
	 * @param mixed offset
	 * @return void 
	 */
	public function offsetUnset($offset)
	{
		return;
	}

	/**
	 * Set a new offset
	 * Part of ArrayAccess
	 *
	 * @param mixed offset
	 * @param mixed value
	 * @return void 
	 */
	public function offsetSet($configName, $configValue)
	{
		// These are configuration values, we cannot assign them by simply
		// saying $cfg[] = $var.
		if($offset == null || empty($offset))
			return;

		// Stores it in the DB
		$rowCount = Doctrine_Query::create()
			->update("{$this->tableName} c")
			->set('c.config_value = ?', $configValue)
			->where('c.config_name = ?', $configName)
			->execute();

		if($rowCount < 1)
		{
			// Insert it instead
			$config = new {$this->tableName}();

			$config->config_name = $configName;
			$config->config_value = $configValue;
			$config->save();
		}

		$this->configVals[$offset] = $value;
		return;
	}
}
