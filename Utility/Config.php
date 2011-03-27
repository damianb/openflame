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
	 * @var - config keys to delete when the script is done
	 */
	protected $toDelete = array();

	/*
	 * @var - config keys to add when the script is done
	 */
	protected $toInsert = array();

	/*
	 * @var - config keys to update when the script is done
	 */
	protected $toUpdate = array();

	/*
	 * Bulk set the keys
	 *
	 * @param array - Key/value configuration
	 */
	public function bulkSet($data)
	{
		$this->data = array_merge($this->data, $data);
	}

	/*
	 * Get the queue
	 *
	 * @return array - all they keys to sync to their new values in the storage system
	 */
	public function getQueues()
	{
		return array($toDelete, $toUpdate, $toInsert);
	}

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
	 * @param mixed - Offset to get
	 * @return mixed - The value at the offset
	 */
	public function offestGet($offset)
	{
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}

	/*
	 * ArrayAccess - Set
	 * Does not allow setting of 
	 *
	 * @param mixed - Key to store the new value in
	 * @param mixed - New value
	 * @return void
	 */
	public function offestSet($offset, $value)
	{
		if($offset == null)
		{
			throw new RuntimeException('The configuration manager cannot accept empty keys');
		}

		if(isset($this->data[$offset]))
		{
			$toUpdate[] = $offset;
		}
		else
		{
			$toInsert[] = $offset;
		}
		
		$this->data[$offset] = $value;
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
		$toDelete[] = $offset;
	}
}
