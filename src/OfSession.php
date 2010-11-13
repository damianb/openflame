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
 * @uses OfConfig.php
 *   - session.savepath
 *   - session.length 
 *   - session.val.iplevel
 *   - session.cookie.name
 *   - session.cookie.path
 *   - session.cookie.domain
 *   - session.cookie.secure
 *   - session.cookie.lifetime
 *
 */

if(!defined('OF_ROOT')) exit;

/**
 * OpenFlame Web Framework - Session class
 * 	    Acts as a wrapper for the native PHP sessions with increased security and
 *		authentication capabilities. Only to be used as a parent class to an
 *		application-level OfUser.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
abstract class OfSession
{
	/**
	 * Authenticate user (Abstract)
	 *
	 * Authenticates user upon login. Must place validated ID in $this->userId
	 *
	 * @returns bool true if authenticated, false if failed
	 */
	abstract protected function authenticateUser();

	/**
	 * Fills user data (Abstract)
	 *
	 * Places the data associated with the user in $this->userId inside the
	 * $this->data array. Switches $this->val['isLoggedIn'] to true.
	 *
	 * @return void
	 */
	abstract protected function fillUserData();

	/**
	 * Validate Auto Login (Abstract)
	 *
	 * Validates the Auto Login data. Places validated id in $this->userId
	 *
	 * @return bool true if passed, false if failed.
	 */
	abstract protected function validateAutoLogin();

	/**
	 * Update User (Abstract)
	 *
	 * This function will allow the extending class to run database updates
	 * on the user that is currently logged in.
	 *
	 * @return void
	 */
	abstract protected function updateUser();

	/**
	 * @var array data
	 *
	 * Reference to $_SESSION['data']
	 */
	public $data = array();

	/**
	 * @var array val
	 *
	 * Reference to $_SESSION['val'] for session validation data
	 */
	public $val = array();

	/**
	 * @var string IP
	 *
	 * IP Address
	 */
	public $ip = '';

	/**
	 * @var string userId
	 *
	 * ID of the user as refered to in the application level. Can be
	 * any type of data - string or int. 
	 */
	protected $userId = '';

	/**
	 * Constructor
	 *
	 * Ensures everything is set properly and starts the PHP session
	 * Must be called in the constructor in the main class
	 *
	 * @return void
	 */
	protected function init()
	{
		// get some settings set
		session_save_path(OF_ROOT . Of::$cfg['session.savepath']);
		session_name(Of::$cfg['session.cookie.name'] . '_sid');

		// All our custom cookie settings
		session_set_cookie_params(
			Of::$cfg['session.cookie.lifetime'],
			Of::$cfg['session.cookie.path'],
			Of::$cfg['session.cookie.domain'],
			Of::$cfg['session.cookie.secure'],
			true);

		$this->now  = time();

		session_start();

		// Our session vars, we should not need to use $_SESSION at all after this
		$this->data	= &$_SESSION['data'];
		$this->val	= &$_SESSION['val'];

		// This IP should update at each page load. val['ip'] will update after
		// validation to match
		$this->ip	= !empty($_SERVER['REMOTE_ADDR']) ? htmlspecialchars($_SERVER['REMOTE_ADDR']) : '';
	}

	/**
	 * Begin and validate the session
	 *
	 * @return void
	 */
	public function sessionBegin()
	{
		// Validate the session
		// it will take care of checking for an empty session
		$this->validateSession();

		// First, check to see if they are not logged in
		if(!$this->val['isLoggedIn'])
		{
			// Now, we can see if they failed autologin or not.
			if(!$this->val['failedAutoLogin'])
			{
				// Validate it
				if($this->validateAutoLogin())
				{
					// Should fill ->data with the user id in ->userId
					$this->fillUserData();
				}
				else
				{
					// Set this to true so we wont come back to validate
					// the autologin again
					$this->val['failedAutoLogin'] = true;
				}
			}
			else
			{
				// Logged in and failed autologin... we don't have anything to give them
			}
		}
		else
		{
			// User has been logged in
			// Run update queue
			$this->updateUser();
		}

		// Lastly, update the session expire so they will be good to browse 
		// until they stop clicking for the duration of the session.length
		$this->val['sessionExpire'] = $this->now + Of::$cfg['session.length'];
	}

	/**
	 * Destory all session data and generate a new SID
	 *
	 * @return void
	 */
	public function sessionKill()
	{
		// Let PHP take it from here
		session_destroy();
		session_start();

		// Initialize an empty session
		$this->sessionCreate(true);
	}

	/**
	 * Creates a new session
	 *
	 * Called to genereate a new, empty session
	 *
	 * @param bool forceNewSid Force a new Session ID or not
	 *
	 * @return void;
	 */
	protected function sessionCreate($forceNewSid = false)
	{
		// Regenerate the ID
		if($forceNewSid)
		{
			session_regenerate_id(true);
		}

		// Create new variables to validate the session
		$this->val = array(
			'userAgentHash'		=> md5($_SERVER['HTTP_USER_AGENT']),
			'sessionExpire'		=> $this->now + Of::$cfg['session.length'],
			'isLoggedIn'		=> false,
			'sessionIp'			=> $this->ip,
			'failedAutoLogin'	=> false,
		);

		// Empty the array
		$this->data = array();
	}

	/**
	 * Validate the session
	 *
	 * Ensures the user returning is the same user. Will force a new session
	 * if it fails validation.
	 *
	 * @return bool true if success, false if failure
	 */
	private function validateSession()
	{
		$validStaus	= true;
		$newSid		= true;

		if(sizeof($this->val))
		{
			// Check for expired sessions
			if($this->now > $this->val['sessionExpire'])
			{
				$validStaus = false;
			}

			// Validate our User Agent
			// User agents should never change between page loads and hold the same
			// cookie. Otherwise we can assume they ar eup to no good.
			if($this->val['userAgentHash'] != md5($_SERVER['HTTP_USER_AGENT']) && $validStaus)
			{
				$validStaus = false;
			}

			// Validate IP
			// This is tricky... we don't want to validate too much, addtionally
			// we have IPv4 and v6 to support. First we see which version
			if(Of::$cfg['session.val.iplevel'] > 0 && $validStaus)
			{
				if(strpos($this->ip, ':'))
				{
					// IPv6
					// @TODO - Get partial validation working or continue to assume
					// everyone using IPv6 will have thier own IP for the duration of
					// the session
					$sessionIP = $this->val['sessionIp'];
					$currentIP = $this->ip;
				}
				else
				{
					// IPv4
					// Easy...
					$sessionIP = implode('.', array_slice(explode('.', $this->val['sessionIp']), 0, Of::$cfg['session.val.iplevel']));
					$currentIP = implode('.', array_slice(explode('.', $this->ip), 0, Of::$cfg['session.val.iplevel']));
				}

				// Now do the all-important check
				if($sessionIP !== $currentIP)
				{
					$validStaus = false;
				}
			}
		}
		else
		{
			$newSid	= false;
		}

		// Do we create a new, empty session?
		if(!$validStaus)
		{
			$this->sessionCreate($newSid);
			$this->fillUserData();
		}

		return $validStaus;
	}

	/**
	 * Login
	 *
	 * Will determine if the user is authenticated 
	 * 
	 * @return bool true if logged in, false if not
	 */
	public function login()
	{
		// If authenticateUser is true, $this->userId will contain the 
		// application's userId
		if($this->authenticateUser())
		{
			// Go to regenerate the session ID
			$this->sessionCreate(true);
			$this->fillUserData();

			// Run queue
			$this->updateUser();

			return true;
		}

		// Return false and let the applicaiton handle the rest
		return false;
	}

	/**
	 * Sets a cookie
	 *
	 * @param string name The name of the cookie var
	 * @param string value Cookie var value
	 * @param int expireTime UNIX timestamp of expiration
	 *
	 * @return void
	 */
	public function setCookie($name, $value, $expireTime = -1)
	{
		// If they did not specify a value, we are giving it our default
		if($expireTime < 0)
		{
			$expireTime = $this->now + Of::$cfg['session.cookie.lifetime'];
		}

		// Set all our parameters
		$name_data	= rawurlencode(Of::$cfg['session.cookie.name'] . '_' . $name) . '=' . rawurlencode($value);
		$expire		= ($expireTime) ? '; expires=' . gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expireTime) : '';
		$path		= (Of::$cfg['session.cookie.path']) ? '; path=' . Of::$cfg['session.cookie.path'] : '';
		$domain 	= (Of::$cfg['session.cookie.domain']) ? '; domain=' . Of::$cfg['session.cookie.domain'] : '';
		$secure		= (Of::$cfg['session.cookie.secure']) ? '; secure' : '';

		// It's header time!
		header('Set-Cookie: ' . $name_data . $expire . $path . $domain . $secure . '; HttpOnly', false);
	}
}
