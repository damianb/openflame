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

namespace OpenFlame\Framework\Session\Storage;
use \OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Sessions Engine interface,
 * 		Sessions engine prototype, declares required methods that a sessions engine must define in order to be valid.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
interface EngineInterface
{
	/*
	 * Init
	 *
	 * Called when the session object is created but before the session has started
	 * @param array - key, value pairs of config options and their values; implemented per driver.
	 * @return void
	 */
	public function init($options);

	/*
	 * Load Session
	 *
	 * Load the session for use by the driver
	 * @return bool - True if a session was found, false if not
	 */
	public function loadSession($sid);

	/*
	 * New Session
	 *
	 * Called when a new session needs to be created
	 * @param bool - Clear the session data? Useful to set true when a session does not validate
	 * @return string - New SID
	 */
	public function newSession($clearData = false);

	/*
	 * Delete Session
	 *
	 * Deletes the currently loaded session
	 * @return void
	 */
	public function deleteSession();

	/*
	 * Load Session Data
	 *
	 * Get the current session data
	 * @return array - arbitrary array of identical structure to one being stored
	 */
	public function loadData();

	/*
	 * Store Session Data
	 *
	 * @param array - arbitrary array
	 */
	public function storeData($data);

	/*
	 * Garbage collection
	 * Should be called periodically
	 */
	public function gc();
}
