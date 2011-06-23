<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  twig
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Twig;
use OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Template variable management class,
 * 	     Manages template variables for the template system.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Variables implements \ArrayAccess
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
		$this->data = array_merge_recursive($this->data, $var_data);
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
