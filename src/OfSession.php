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
	 * @param $cookie_name
	 *
	 * Name of the cookie
	 */
	protected $cookie_name = '';

	/**
	 * @param $settings
	 *
	 * Session settings
	 */
	private $settings = array();

	/**
	 * IP validation level flags
	 */
	const VALIDATE_NONE		= 0;
	const VALIDATE_FIRST	= 1;
	const VALIDATE_SECOND	= 2;
	const VALIDATE_THIRD	= 3;
	const VALIDATE_ALL		= 4;

	/**
	 * Constructor
	 *
	 * @param string $session_save_path Path to store the sessions
	 */
	public function __construct()
	{
		// Mirror _SESSION
		$this->_session_vars = &$_SESSION;
		
		// Set some defaults
		$this->settings = array(
			'cookie_lifetime'	=> 0,
			'cookie_path'		=> '/',
			'cookie_domain'		=> ((substr_count($_SERVER['HTTP_HOST'], '.') < 2) ? '.' : '') . $_SERVER['HTTP_HOST'],
			'cookie_secure'		=> false,
			'validate_ip'		=> self::VALIDATE_THIRD,
			'validate_ua'		=> true,
		);
	}

	/**
	 * Set session save path
	 *
	 * @param string $save_path Path to store the sessions
	 * @return object
	 */
	public function setSessionSavePath($save_path)
	{
		session_save_path($save_path);
		
		return $this;
	}

	/**
	 * Set cookie name
	 *
	 * @param string $cookie_name Cookie name (rather prefixes to all the cookies)
	 * @return object
	 */
	public function setCookieName($cookie_name)
	{
		// Sotre this for later
		$this->cookie_name = $cookie_name;

		// We really are not namming the cookie directly, we are just naming the session
		session_name($cookie_name . '_sid');
	}

	/**
	 * Set session cookie life
	 *
	 * @param int $cookie_life Lifetime (in seconds) to set the cookie
	 * @return object
	 */
	public function setSessionCookieLife($cookie_life)
	{
		$this->settings['cookie_life'] = (int) $cookie_life;

		return $this;
	}

	/**
	 * Set session cookie path
	 *
	 * @param int $cookie_path Lifetime (in seconds) to set the cookie
	 * @return object
	 */
	public function setSessionCookiePath($cookie_path)
	{
		$this->settings['cookie_path'] = $cookie_path;

		return $this;
	}

	/**
	 * Set session cookie domain
	 *
	 * @param string $cookie_domain Cookie domain (must have two dots)
	 * @return object
	 */
	public function setSessionCookieDomain($cookie_domain)
	{
		$this->settings['cookie_domain'] = $cookie_domain;

		return $this;
	}

	/**
	 * Set session cookie secure
	 *
	 * @param bool $cookie_secure Set to true if trasnmitting over https
	 * @return object
	 */
	public function setSessionCookieSecure($cookie_secure)
	{
		$this->settings['cookie_secure'] = (bool) $cookie_secure;

		return $this;
	}

	/**
	 * Set sessin IP validation level
	 *
	 * @param int $level Flag from the class constants
	 * @return object
	 */
	public function setSessionIpValidation($level)
	{
		// Check for bad values
		if($level <= self::VALIDATE_ALL || $level >= self::VALIDATE_NONE)
			$this->settings['validate_ip'] = $level;
		
		return $this;
	}

	/**
	 * Validate User Agent
	 *
	 * @param bool $validate Validate the user agent?
	 * @return object
	 */
	public function validateUserAgent($level)
	{
		$this->settings['validate_ua'] = (bool) $level;

		return $this;
	}

	/**
	 * Session Start
	 * Starts a new session
	 *
	 * @return object
	 */
	public function sessionStart()
	{
		session_set_cookie_params(
			$this->settings['cookie_lifetime'], 
			$this->settings['cookie_path'], 
			$this->settings['cookie_domain'], 
			$this->settings['cookie_secure']
		);
		
		// Let PHP take it from here
		session_start();
	
		// Validate session IP
		if(empty($_SESSION['valid_ip']) || $this->settings['validate_ip'] == self::VALIDATE_NONE)
		{
			$_SESSION['valid_ip'] = $_SERVER['REMOTE_ADDR'];
		}
		else
		{
			$session_ip = explode('.', $_SESSION['valid_ip']);
			$current_ip = explode('.', $_SERVER['REMOTE_ADDR']);
			
			// It will loop through each part of the IP
			$not_valid = false;
			for($i = 0; $i < $this->settings['validate_ip']; $i++)
			{
				if($session_ip[$i] != $current_ip[$i])
				{
					$not_valid = true;
					break;
				}
			}
			
			// Remove their session vars (effectivly logging them out) but 
			// don't destory the session, we can still use it.
			if($not_valid)
				session_unset();
		}
		
		// Validate User Agent
		if($this->settings['validate_ip'])
		{
			if(empty($_SESSION['valid_ua']))
			{
				$_SESSION['valid_ua'] = $_SERVER['HTTP_USER_AGENT'];
			}
			else
			{
				// Here is where we check for spys
				if($_SESSION['valid_ua'] != $_SERVER['HTTP_USER_AGENT'])
					session_unset(); // SPY!
			}
		}

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
		session_unset();
		session_destroy();
	}
}
