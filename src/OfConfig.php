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
			->from($tableName);
		$rawConfig = $query->fetchArray();

		// Throw them in an array
		foreach($rawConfig as $data)
			$this->configVals[$data['config_name']] = is_numeric($data['config_value']) ? (int) $data['config_value'] : (string) $data['config_value'];

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
		return !empty($this->configVals[$configName]) ? $this->configVals[$configName] : null;
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
		if(empty($configName) || empty($configValue) || $this->configVals[$configName] == $configValue)
			return;

		if(isset($this->configVals[$configName]))
		{
			// update our existing value
			Doctrine_Query::create()
				->update($this->tableName)
				->set('config_value', '?', $configValue)
				->where('config_name = ?', $configName)
				->execute();
		}
		else
		{
			// Not there? insert it
			$tableName = &$this->tableName;
			$config = new $tableName();

			$config->config_name = $configName;
			$config->config_value = $configValue;
			$config->save();
		}

		$this->configVals[$configName] = $configValue;
		return;
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
	 * Part of ArrayAccess
	 *
	 * @param mixed offset
	 * @return void 
	 */
	public function offsetUnset($configName)
	{
		Doctrine_Query::create()
			->delete()
			->from($this->tableName)
			->andWhere('config_name = ?', $configName)
			->execute();

		unset($this->configVals[$configName]);

		return;
	}
}
