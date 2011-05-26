<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  session
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Session\Autologin;
use \OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Framework - Sessions Autologin Engine interface
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
interface EngineInterface
{
	/**
	 * Set options
	 * @param array - Key/value pair
	 */
	public function setOptions($options);

	/*
	 * Store a key/uid
	 * @param string key
	 * @param string uid
	 */
	public function store($key, $uid);

	/*
	 * Lookup autologin by key and delete the old one
	 * @param string - key from the user
	 * @return string - UID stored associated with the key or null
	 */
	public function lookup($key);

	/*
	 * Garbage collection
	 * Should be called periodically
	 */
	public function gc();
}
