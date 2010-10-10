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
 * @uses OfSession
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
class OfUser extends OfSession
{
	/**
	 * @var $data
	 *
	 * Contains all the data that is stored in the Users table
	 * Refernce to OfSession::$php_session_vars (which is a reference to $_SESSION)
	 */
	public $data = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Just make sure all this is called
		parent::__construct();
		
		// Get our stacked refs set up
		$this->data = &$this->_session_vars;
	}

	/**
	 * Check persistent login
	 * Used as a fluid interface function with sessionStart()
	 * Loads up the user data
	 * 
	 * @return void
	 */
	public function checkPersistent()
	{
	}
	
	/**
	 * Format date
	 * Takes the user preference for the date format and turns the passed timestap 
	 * into a valid date
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
	public function login($username, $password, $auto_login = false, $redirect_success = '', $redirect_failure = '')
	{
	}

	/**
	 * Loads user data into the $data property
	 *
	 * @param 
	 */
	public function loadUser($user_id, $data)
	{
	
	}

	/**
	 * Get random text
	 *
	 * @param string $type Can be 'string' (full alpha-numeric),  'hex' (0-9, f-f), or 'int' (0-9)
	 * @param int $length How long? 
	 *
	 * @return mixed requested string 
	 */
	public function getRandom($type, $length = 10)
	{
		$mctime = microtime();
		$text = md5($mctime . base_convert(mt_rand(150, 500), 10, 36));

		// Get the type we requested
		switch($type)
		{
			case 'string': 
				$text = base_convert($text, 16, 36);
			break;
			
			case 'int':
				$text = (int) base_convert($text, 16, 10);
			break;

			case 'hex':
				// Nothing here (already hex)
			break;
		}
		
		// Cut off the string after the specified length
		return substr($text, 0, $length);
	}
}
