<?php
/**
 *
 * @package OpenFlame Web Framework
 * @copyright (c) 2010 OpenFlameCMS.com
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - PDO extender
 * 	     Gives some more functionality to PDO.
 *
 *
 * @author      Sam Thompson ("Sam")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 */
class OfDb
{
	/**
	 * @var PDO connection object
	 */
	private $PDOconn;

	/**
	 * @var array of PDO Statement objects
	 */
	private $stmt = array();
	
	public function __construct()
	{
	}
	
	public function connect($dsn, $username, $password, $options = array())
	{
		try
		{
			$this->PDOconn = new PDO($dsn, $username, $password, $options);
		}
		catch(PDOException $e)
		{
			echo 'Connection Failed: ' . $e->getMessage();
			exit;
		}
	}

	/**
	 * Query...
	 *
	 *
	 * @return int PDO statement object 
	 */
	public function query($sql, $queryType = '', $sqlAry = array())
	{
		if(sizeof($sqlAry) && ($queryType == 'SELECT' || $queryType == 'INSERT' || $queryType == 'UPDATE' || $queryType == 'DELETE'))
		{
			$columNames = array_keys($sqlAry);
			
			switch($queryType)
			{
				// Select, Delete, Update all work the same at this level
				case 'SELECT':
				case 'DELETE':
				case 'UPDATE':
					$sql_where = array();
					
					foreach($columnNames as $column)
						$sql_where[] = $column . ' = :' . $column;
					
					$_sql = implode(',', $sql_where);
				break;
				
				case 'INSERT':					
					$_sql = '(' . implode(',', $columnNames) . ")\n VALUES(:" .  implode(',:', $columnNames) . ')';
				break;
			}
			
			$sql = sprintf($sql, $_sql);
		}
		
		end($this->stmt);
		$stmtId = key($this->stmt);
		
		$this->stmt[$stmtId] = $this->PDOconn->prepare($sql);
		
		if(isset($_sql))
		{
			foreach($sqlAry as $column => $value)
				$this->stmt[$stmtId]->bindParam(':' . $column, $value);
		}
		
		$this->stmt[$stmtId]->execute();
		return $stmtId;
	}

	public function rowsAffected($stmtId)
	{
		return $this->stmt[$stmtId]->rowCount();
	}
	
	public function lastInsertId()
	{
		return $PDOconn->lastInsertId();
	}
	
}