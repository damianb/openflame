<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 *
 * @uses OfDb.php
 * @uses OfConfig.php
 * @uses OfInput.php
 */

if(!defined('OF_ROOT')) exit;

/**
 * OpenFlame Web Framework - Session class
 * 	    Acts as a wrapper for the native PHP sessions with increased security and
 *		authentication capabilities
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfSession
{
	/**
	 * Reference to $_SESSION for sessions/user abstraction
	 */
	protected $_session_vars = array();
	
	/**
	 * Anonymous user id
	 */
	const ANONYMOUS_USER = 0;

	/**
	 * Constructor
	 *
	 * @param string $session_save_path Path to store the sessions
	 */
	public function __construct()
	{
		$this->_session_vars = &$_SESSION;
	}

	/**
	 * Set session save path
	 *
	 * @param string $session_save_path Path to store the sessions
	 */
	public function setSessionSavePath($session_save_path)
	{
		// Set some defaults for the session handler to reflect our application-
		// level configuration values.
		session_save_path($session_save_path);
	}
	
	/**
	 * Set cookie params
	 * Wrapper for the session configuraiton function session_set_cookie_params()
	 *
	 * @param int $cookie_lifetime Time in seconds after setting the cookie will expire
	 * @param string $cookie_path Web path of the cookie
	 * @param string $cookie_domain Domain the cookie is active in, ensure it has two dots (".")
	 * @param bool $cookie_secure is the cookie being sent over https?
	 *
	 * @return void
	 */
	public function setCookieParams($cookie_lifetime, $cookie_path, $cookie_domain, $cookie_secure = false)
	{
		session_set_cookie_params($cookie_lifetime, $cookie_path, $cookie_domain, $cookie_secure);
	}

	/**
	 * Session Start
	 * Starts a new session
	 *
	 * @return void
	 */
	public function sessionStart()
	{
		// Let PHP take it from here
		session_start();

		// Fluid interface
		return $this;
	}

	/**
	 * Session Kill
	 * Destorys a session, should be used ONLY on logout
	 *
	 * @return void
	 */
	public function sessionKill()
	{
		// Let PHP clean up the trash
		session_destroy();
	}
}
