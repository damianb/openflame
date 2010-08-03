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

class OfInput
{
	/**
	 * @var mixed raw input 
	 */
	private $rawInput;

	/**
	 * @var mixed cleaned input
	 */
	private $cleanedInput;

	/**
	 * Constructor 
	 *
	 * @param string $varName Var name in the global you're after
	 * @param mixed $default Default value and type to fall back on and check for good types
	 * @param string @globalName The name of the super global to use. REQUEST, GET, POST, COOKIE, and SERVER are all avavible.
	 */
	public function __construct($varName, $default, $globalName = '_REQUEST')
	{
		// Prepend the _ if not there
		if($globalName[0] != '_')
			$globalName = '_' . $globalName;
		
		// We should have a good global now.
		$globalName = in_array($globalName, array('_REQUEST', '_GET', '_POST', '_COOKIE', '_SERVER')) ? $globalName : '_REQUEST';
		
		// We need to make sure that cookie is not contaminating the value of request
		if($globalName == '_REQUEST' && isset($_COOKIE[$varName]))
			$_REQUEST[$varName] = isset($_POST[$varName]) ? $_POST[$varName] : $_GET[$varName];
		
		// Assign the raw var
		$this->rawInput = $GLOBALS[$globalName][$varName];
		
		$this->cleanedInput = $this->cleanVar($GLOBALS[$globalName][$varName], $default);
	}

	/**
	 * Recursively digs through the default to ensure everything is in it's place. 
	 *
	 * @param mixed $var
	 * @param mixed $defaultType
	 * @return mixed The cleaned data.
	 */
	private function cleanVar($var, $default)
	{
		if(is_array($var))
		{
			list($_keyDefault, $_valueDefault) = each($default);

			array_walk($var, array(&$this, '_cleanVar'), $_valueDefault);
		}
		else
		{
			$this->bindVar($var, $default);
		}
		
		return $var;
	}
	
	/**
	 * Helper method for OfInput::cleanVar(), aids in cleaning deep arrays quickly
	 * 
	 * @param mixed &$value
	 * @param mixed &$key
	 * @param mixed $default
	 */
	private function _cleanVar(&$value, &$key, $default)
	{
		if(is_array($value))
		{
			list($_keyDefault, $_valueDefault) = each($default);
			array_walk($value, array(&$this, '_cleanVar'), $_valueDefault);
			$this->bindVar($key, $_keyDefault);
		}
		else
		{
			list($value, $key) = array($this->bindVar($value, $_valueDefault), $this->bindVar($key, $_keyDefault));
		}
	}

	/**
	 * Binds the var to it's final type
	 *
	 * @param mixed $var The var being bound
	 * @param mixed $default Default value
	 * @return string Cleaned output
	 */
	public function bindVar($var, $default)
	{
		$type = gettype($default);
		settype($var, $type);
		
		if($type == 'string')
		{
			if(!mb_check_encoding($var))
				$var = $default;
			
			$var = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $var), ENT_COMPAT, 'UTF-8'));
		}
		
		return $var;
	}

	/**
	 * Validates the piece of data
	 *
	 * @param string $type Validate against, see the full type profile list
	 * @return bool true if valid, false if not.
	 */
	public function validate($type, $min = 0, $max = 0)
	{
		switch($type)
		{
			case '':
			break;
		}
	}

	/**
	 * Returns the raw var
	 *
	 * @return bool true if set, false if not.
	 */
	public function getRaw()
	{
		return $this->rawInput;
	}

	/**
	 * Returns the cleaned var
	 *
	 * @return bool true if set, false if not.
	 */
	public function getClean()
	{
		return $this->cleanedInput;
	}

	/**
	 * Checks to see if the var was even set when the page was submitted
	 *
	 * @return bool true if set, false if not.
	 */
	public function wasSet()
	{
		return $this->rawInput == null ? true : false;
	}
}
