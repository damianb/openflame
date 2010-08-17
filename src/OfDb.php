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
 * OpenFlame Web Framework - PDO extender
 * 	     Gives some more functionality to PDO.
 *
 *
 * @author      Sam Thompson ("Sam")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
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

	/**
	 * (Empty) Constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Open new connection to the database
	 *
	 * @param string $dsn The string PDO requires to open a new database connection
	 * @param string $username user used to log into the database specified
	 * @param string $password the password to the user account specified in the param before
	 * @param array $options dbms specific options given to PDO
	 * @return void
	 */
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
	 * This method will allow you to build a query and run it at once. You may run a normal SQL query by simply placing
	 * the query string as the first parameter and ommiting the rest. If you want to build an array simillar to the
	 * dbal::sql_buld_array() function, you may do that too, where the 2nd paramter is the query type (SELECT, INSERT,
	 * UPDATE, or DELTE), and the 3rd is an array associated by the column names. All values are escaped via prepared
	 * statements.
	 *
	 * @param string $sql The SQL query
	 * @param string $queryType If you are building a query, it's SELECT, INSERT, UPDATE, or DELTE, otherwise, ommit this
	 * @param array $sqlAry If you are buildiing a query, this array must be an array of value associated by the column names.
	 * @return int PDO statement object
	 */
	public function query($sql, $queryType = '', $sqlAry = array())
	{
		// Make sure this is uppercase
		$queryType = strtoupper($queryType);

		// If we are building an array
		if(sizeof($sqlAry) && in_array($queryType, array('SELECT', 'INSERT', 'UPDATE', 'DELETE')))
		{
			$columNames = array_keys($sqlAry);

			switch($queryType)
			{
				// These three behave similarly
				case 'SELECT':
				case 'DELETE':
				case 'UPDATE':
					$sqlWhere = array();

					// we are creating the prepared statment for bindParam
					// column_name = :column_name
					foreach($columnNames as $column)
						$sqlWhere[] = $column . ' = :' . $column;

					$separator = ($queryType == 'UPDATE') ? ',' : ' AND ';
					$_sql = implode($separator, $sqlWhere);
				break;

				case 'INSERT':
					$_sql = '(' . implode(',', $columnNames) . ")\n VALUES(:" .  implode(',:', $columnNames) . ')';
				break;
			}

			// We hope that people will put a %s where they want the query built.
			$sql = sprintf($sql, $_sql);
		}

		// We need to get the next available key in the array
		end($this->stmt);
		$stmtId = 1+ key($this->stmt);

		// Prepare the statment
		$this->stmt[$stmtId] = $this->PDOconn->prepare($sql);

		// If we are building a query, bind the params
		if(isset($_sql))
		{
			foreach($sqlAry as $column => $value)
				$this->stmt[$stmtId]->bindParam(':' . $column, $value);
		}

		// Now, execute the query, and return our statment id so it can be refrenced
		// when we get the results from it.
		$this->stmt[$stmtId]->execute();
		return $stmtId;
	}

	/**
	 * Grab the rows affected by a query
	 *
	 * @param int prepared statement id as returned by OfDb::query()
	 * @return int number of rows affected
	 */
	public function rowsAffected($stmtId)
	{
		return $this->stmt[$stmtId]->rowCount();
	}

	/**
	 * Get the last insertion id
	 *
	 * @return int last auto_incriment id created by the last INSERT query.
	 */
	public function lastInsertId()
	{
		return $this->PDOconn->lastInsertId();
	}
}
