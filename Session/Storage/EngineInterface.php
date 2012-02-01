<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  session
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Session\Storage;
use \emberlabs\openflame\Event\Instance as Event;

/**
 * OpenFlame Framework - Sessions Engine interface,
 * 		Sessions engine prototype, declares required methods that a sessions engine must define in order to be valid.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
interface EngineInterface
{
	/*
	 * Initialized the engine
	 * @param array options - Associative array of options
	 * @return void
	 */
	public function init(array &$options);

	/*
	 * Load data associated with the SID
	 * @param string sid - Session ID (Must be [a-z0-9])
	 * @return mixed - Arbitrary data stored
	 */
	public function load($sid);

	/*
	 * Store data associated with the session id
	 * @param string sid - Session ID (Must be [a-z0-9])
	 * @param mixed data - Arbitrary data to store
	 * @return bool - true on success, false on failure
	 */
	public function store($sid, $data);

	/*
	 * Purge session object
	 * Basically giving the Engine the que to kill the data associated with
	 * this session ID.
	 * @param string sid
	 */
	public function purge($sid);

	/*
	 * Garbage Collection
	 * Called at the end of each page load.
	 * @param \emberlabs\openflame\Event\Instance - The event instance, if using this method with the event dispatcher/scheduler.
	 * @return void
	 */
	public function gc(Event $event = NULL);
}
