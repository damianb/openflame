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
 * OpenFlame Web Framework - Twig Integration class,
 * 	     Manages template variables for Twig.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfTwig implements ArrayAccess
{
	/**
	 * @var array - Array of all global template variables
	 */
	protected $data = array();

	/**
	 * Assign a bunch of template vars at once.
	 * @param array $var_data - Array of variables to set.
	 * @return void
	 */
	public function assignVars(array $var_data)
	{
		array_walk($var_data, array($this, '_assignVar'));
	}

	/**
	 * Assigns a specified template var
	 * @param string $var_name - The name of the var to set.
	 * @param mixed $var_value - The value to set the var with.
	 * @return void
	 */
	public function assignVar($var_name, $var_value)
	{
		$this->data[(string) $var_name] = $var_value;
	}

	/**
	 * Assigns a specified template var - backwards of self::assignVar() as this must be used with array_walk()
	 * @param mixed $var_value - The value to set the var with.
	 * @param string $var_name - The name of the var to set.
	 * @return void
	 */
	public function _assignVar($var_value, $var_name)
	{
		$this->data[(string) $var_name] = $var_value;
	}

	/**
	 * Pulls all template vars that have been accumulated.
	 * @return array - Array of all template variables.
	 */
	public function fetchAllVars()
	{
		return $this->data;
	}

	/**
	 * Template use only; fetches a specific template var.
	 * @param string $var_name - The name of the var.
	 * @return mixed - Desired template variable's value.
	 */
	public function fetchVar($var_name)
	{
		return ($this->issetVar($var_name)) ? $this->data[$var_name] : false;
	}

	/**
	 * Template use only; checks to see if a specific template var exists.
	 * @param string $var_name - The name of the var.
	 * @return boolean - Does the var exist?
	 */
	public function issetVar($var_name)
	{
		return isset($this->data[$var_name]);
	}

	/**
	 * ArrayAccess methods
	 */

	/**
	 * Check if an "array" offset exists in this object.
	 * @param mixed $offset - The offset to check.
	 * @return boolean - Does anything exist for this offset?
	 */
	public function offsetExists($offset)
	{
		return $this->issetVar($offset);
	}

	/**
	 * Get an "array" offset for this object.
	 * @param mixed $offset - The offset to grab from.
	 * @return mixed - The value of the offset, or null if the offset does not exist.
	 */
	public function offsetGet($offset)
	{
		return ($this->issetVar($offset)) ? $this->fetchVar($offset) : NULL;
	}

	/**
	 * Set an "array" offset to a certain value, if the offset exists
	 * @param mixed $offset - The offset to set.
	 * @param mixed $value - The value to set to the offset.
	 * @return void
	 */
	public function offsetSet($offset, $value)
	{
		$this->assignVar($offset, $value);
	}

	/**
	 * Unset an "array" offset.
	 * @param mixed $offset - The offset to clear out.
	 * @return void
	 */
	public function offsetUnset($offset)
	{
		unset($this->data[(string) $offset]);
	}
}
