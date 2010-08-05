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
 * OpenFlame Web Framework - User Input Handler
 * 	     Allows for safe user input and validation of such.
 *
 *
 * @author      Sam Thompson ("Sam")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 */
class OfInput
{
	/**
	 * @var mixed raw input 
	 */
	protected $rawInput;

	/**
	 * @var mixed cleaned input
	 */
	protected $cleanedInput;

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
			$var = $this->bindVar($var, $default);
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
			// Validates /any/ email
			case 'email':
				// By "James Watts and Francisco Jose Martin Moreno"
				// Assumed Public Domain
				return preg_match("#^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$#i", $this->cleanedInput) === 1 ? true : false;
			break;

			case 'url':
				return; // @TODO url regex
			break;

			case 'ip4':
				// By "G. Andrew Duthie" (http://regexlib.com/REDetails.aspx?regexp_id=32)
				// Assumed Public Domain
				return preg_match("#^(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])$#", $this->cleanedInput) === 1 ? true : false;
			break;
			
			case 'ip6':
				// By "Stephen Ryan" (http://forums.dartware.com/viewtopic.php?t=452)
				// Assumed Public Domain
				return preg_match("#\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$#i", $this->cleanedInput) === 1 ? true : false;
			break;
			
			// Alpha-numeric chars only
			case 'alphanumeric':
				// Check if they wanted a range
				$range = ($max || $min && $max > $min) ? '{' . $min . ',' . $max . '}' : '';
				
				return preg_match("#^[A-Za-z0-9]*{$range}$#i", $this->cleanedInput) === 1 ? true : false;
			break;

			// Validate a basic string, uses $min, $max
			case 'string':
				return (strlen($this->cleanedInput) >= $min && strlen($this->cleanedInput) <= $max) ? true : false;
			break;
			
			// Validates any int, uses $min and $max for the value of the int, not the size.
			case 'int':
				return ($this->cleanedInput >= $min && $this->cleanedInput <= $max) ? true : false;
			break;
		}
		
		// If we get a bad type or something
		return false;
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
