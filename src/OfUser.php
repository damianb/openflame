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

if(!defined('OF_ROOT')) exit;

/**
 * OpenFlame Web Framework - Session class
 * 	     Acts as a wrapper for the native PHP sessions with increased security and authentication capabilities.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfUser
{
	/**
	 * @var $data
	 *
	 * Contains all the data that is stored in the Users table
	 * Copy of $_SESSION, and can be used dynamically as such.
	 */
	public $data = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
	
	}

	/**
	 * Session Start
	 * Starts a new session
	 *
	 * @return void
	 */
	public function sessionStart()
	{
	}

	/**
	 * Session Kill
	 * Destorys a session
	 *
	 * @return void
	 */
	public function sessionKill()
	{
	}

	/**
	 * Format date
	 * Takes the user preference for the date format and turns the passed timestap 
	 * into a valid date.
	 *
	 * @param int $ts Timestamp, empty one will result in current time()
	 */
	public function formatDate($ts = time())
	{
	}

	/**
	 * Login
	 * Logs in a user, call when you are receiving a login. Must be called after OfUser::sessionStart()
	 *
	 * @param string $username Username of the person to login
	 * @param string $password Plaintext password as inputed by the user
	 * @param bool $auto_login Set to true to allow the user to autologin every time after logging in this time
	 * @param string $redirect_success Path to the page to redirect to after successful login. Defaults to current page
	 * @param string $redirect_failure Path to the page to redirect to after failed login. Defaults to current page
	 */
	public function logIn($username, $password, $auto_login = false, $redirect_success = '', $redirect_failure = '')
	{
	}
}
