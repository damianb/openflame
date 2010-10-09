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
class OfConfig
{
	/**
	 * @var $val
	 *
	 * Array of values to call from when retrieving configs
	 */
	public $val = array();

	/**
	 * @var $table
	 *
	 * Configuration table object from doctrine
	 */
	private $table;
	
	/**
	 * Constructor
	 *
	 * @var string $config_table_name The table name of the config table (keeps it independent of DB constants)
	 */
	public function __construct($config_table_name)
	{
		// Store this for later use
		$this->table = Doctrine::getTable($config_table_name);
		
		// Grab the values
		$query = $this->table->createQuery('c');
		$rawconfig = $query->fetchArray();
		
		// Throw them in an array
		foreach($rawconfig as $name => $value)
			$this->val[$name] = is_numeric($value) ? (int) $value : (string) $value;
		
		return;
	}

	/**
	 * Sets a value to a config key
	 *
	 * @param string $config_name Name of the config key
	 * @param string $config_value Value to be set
	 *
	 * @return void
	 */
	public function set($config_name, $config_value)
	{
		// Try the update
		$row_count = $this->table->createQuery('c')
			->set('c.config_value = ?', $config_value)
			->where('c.config_name = ?', $config_name)
			->execute();
		
		// Do an insert if we have nothing in our affected rows. 
		if(!$row_count)
		{
			$this->table->config_name	= $config_name;
			$this->table->config_value	= $config_value;
			$this->table->save();
		}
		
		// Finally, update the runtime config array
		$this->val[$config_name] = $config_value;
		
		return;
	}
}
