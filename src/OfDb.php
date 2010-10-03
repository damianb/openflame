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
 * OpenFlame Web Framework - Doctrine Interface
 * 	     Places doctrine into an Easy-to-use class to ensure it's objects stay available on a global scope.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfDb
{
	/**
	 * @var manager
	 *
	 * manager object used by Doctrine
	 */
	public $manager;

	/**
	 * @var connection
	 *
	 * Connection object used by Doctrine
	 */
	public $connection;

	/**
	 * @var models_path
	 *
	 * Path to doctrine models.
	 */
	private $models_path = '';

	/**
	 * @const CONNECTION_NAME
	 *
	 * Default name of the connection used by doctrine.
	 */
	const CONNECTION_NAME = 'openflameframework';

	/**
	 * Constructor
	 *
	 * @param string doctrineRoot - Path to doctirne
	 * @param string modelsPath - Path to the models directory
	 */
	public function __construct($doctrine_root, $models_path)
	{
		// Get Doctrine ready to deploy
		require $doctrine_root. 'Doctrine.php';
		spl_autoload_register(array('Doctrine', 'autoload'));
		$this->manager = Doctrine_Manager::getInstance();
		
		$this->models_path = $models_path;
	}

	/**
	 * Connects to the database via Doctrine, sets default configuration, and loads models
	 *
	 * @param string dsn - connection string
	 * @param string connecitonName - name of the connection. Leave default to assume default connection or specify custom
	 *
	 * @return void
	 */
	public function loadDatabase($dsn, $connection_name = '')
	{
		$this->connection = Doctrine_Manager::connection($dsn, (($connection_name) ? $connection_name : self::CONNECTION_NAME));
		
		$this->connection->setCharset('utf8');
		$this->connection->setCollate('utf8_bin');

		// Configure some things
		$this->manager->setAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
		$this->manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);

		spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
		$this->manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);
		
		Doctrine_Core::loadModels($this->modelsPath);
	}

	/**
	 * Loads tables
	 *
	 * @param array tables - array of all the tables to load
	 *
	 * @return void
	 */
	public function loadTables($tables = array())
	{
		foreach($tables as $table)
			$this->{"$table"} = Doctrine_Core::getTable($table);
	}
}
