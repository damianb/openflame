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
	 * @var $insertQueue
	 *
	 * Values queued for insertion
	 */
	private $insertQueue = array();

	/**
	 * @var $updateQueue
	 *
	 * Values queued for insertion
	 */
	private $updateQueue = array();

	/**
	 * @var $deleteQueue
	 *
	 * Values queued for insertion
	 */
	private $deleteQueue = array();

	/**
	 * Constructor
	 *
	 * @var string $tableName The table name of the config table (keeps it 
	 *             independent of DB constants)
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
	 * Destructor
	 *
	 * All this does is call to save() to save our config values.
	 */
	public function __destruct()
	{
		$this->save();
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
		return isset($this->configVals[$configName]) ? $this->configVals[$configName] : null;
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
			$this->updateQueue[$configName] = $configValue;
		}
		else
		{
			$this->insertQueue[$configName] = $configValue;
		}

		if(isset($this->configVals[$configName]))
		{

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
			->where('config_name = ?', $configName)
			->execute();

		unset($this->configVals[$configName]);

		return;
	}

	/**
	 * Save
	 *
	 * Loops through the queues and inserts, updates, or deletes thed data
	 *
	 * @return void
	 */
	public function save()
	{
		// Check for inserts
		if(sizeof($this->insertQueue))
		{
			// Doctrine collecition this time
			$configs = new Doctrine_Collection($this->tableName);

			$i = 0;
			foreach($this->insertQueue as $configName => $configValue)
			{
				$configs[$i]->config_name	= $configName;
				$configs[$i]->config_value	= $configValue;

				$i++;
			}

			// Save and empty queue
			$configs->save();
			$this->insertQueue = array();
		}

		// Updates
		if(sizeof($this->updateQueue))
		{
			// Here is where we're going to encounter a query in a loop...
			foreach($this->updateQueue as $configName => $configValue)
			{
				// update our existing value
				Doctrine_Query::create()
					->update($this->tableName)
					->set('config_value', '?', $configValue)
					->where('config_name = ?', $configName)
					->execute();
			}
			// ..Oh well
			
			// Empty queue
			$this->updateQueue = array();
		}

		// Deletions
		if(sizeof($this->deleteQueue))
		{
			// $this->deleteQueue works a little differently, there are no
			// values, just the configNames as the values are not needed
			Doctrine_Query::create()
				->delete()
				->from($this->tableName)
				->whereIn('config_name', $this->deleteQueue)
				->execute();

			// Piece of cake, empty the queue now
			$this->deleteQueue = array();
		}
	}
}
