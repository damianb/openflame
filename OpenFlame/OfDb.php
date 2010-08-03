<?php
/**
 *
 * @package OpenFlame Web Framework
 * @version $Id$
 * @copyright (c) 2010 OpenFlameCMS.com
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

class OfDb extends PDO
{
	public function __construct()
	{
	}
	
	public function connect($dsn, $username, $password, $options = array())
	{
		try 
		{
			$dbObj = parent::__construct($dsn, $username, $password, $options);
		}
		catch(PDOException $e)
		{
			echo 'Connection Failed: ' . $e->getMessage();
			exit;
		}
		
		return $dbObject;
	}

	public function prepare($sql, $sql_ary = array())
	{
		switch($type)
		{
			case 'SELECT':
			case 'DELETE':
				
			break;
			
			case 'UPDATE':
				
			break;
			
			case 'INSERT':
				
			break;
		}
	}
}