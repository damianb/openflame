<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Utility;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - General Configuration Manager
 * 	     Provides an applicaiton-level configuration manager
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Config implements ArrayAccess
{
	/*
	 * @var - all the data that used by the array
	 */
	protected $data = array();

	/*
	 * ArrayAccess - Exists
	 *
	 * @param mixed - Offest to unset
	 * @return bool - Wheather or not the offset exists
	 */
	public function offestExists($offset)
	{
		return isset($this->data[$offset]) ? true : false;
	}

	/*
	 * ArrayAccess - Get
	 *
	 * @param mixed - Offest to get
	 * @return mixed - The value at the offset
	 */
	public function offestGet($offset)
	{
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}

	/*
	 * ArrayAccess - Set
	 *
	 * @param mixed - Key to store the new value in
	 * @param mixed - New value
	 * @return void
	 */
	public function offestSet($offset, $value)
	{
		if($offset == null)
		{
			$this->data[] = $value;
		}
		else
		{
			$this->data[$offset] = $value;
		}
	}

	/*
	 * ArrayAccess - Unset
	 *
	 * @param mixed - Offest to unset
	 * @return void
	 */
	public function offestUnset($offset)
	{
		unset($this->data[$offset];
	}
}
