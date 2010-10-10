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
	 * @var $table
	 *
	 * Contains the doctrine table object
	 */
	public $table;

	/**
	 * Anonymous user id
	 */
	const ANONYMOUS_USER = 0;

	/**
	 * Constructor
	 *
	 * @param string $user_table_name Name of the users table
	 */
	public function __construct($users_table_name)
	{
		// Just make sure all this is called
		parent::__construct();
		
		// Get our stacked refs set up
		$this->data = &$this->_session_vars;
		
		// Now, set our table object as a property so we can access it from anywhere
		$this->table = Doctrine::getTable($users_table_name);
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
		// Get our cookies
		$c_user_id	= $_COOKIE[$this->cookie_name . '_uid'];
		$c_pl_id	= $_COOKIE[$this->cookie_name . '_pl'];

		// If they are already logged in or they dont have cookies don't bother with this
		if($this->data['user_id'] != self::ANONYMOUS_USER || empty($_user_id) || empty($c_pl_id))
			return;

		// Now, we check if we have an entry in the DB
		$query = $this->table->createQuery('u')
			->where('u.user_id = ?', $c_user_id)
			->andWhere('u.session_pl', $c_pl_id);
		$user_row = $query->fetchOne();
		
		// send them out if we have no match
		if(empty($user_row['user_id']))
			return;

		// $user_row is an object, we have to loop through it to trigger arrayAccess
		foreach($user_row as $key => $value)
			$this->data[$key] = $value;
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
	 * Get random text
	 *
	 * @param string $type Can be 'string' (full alpha-numeric),  'hex' (0-9, f-f), or 'int' (0-9)
	 * @param int $length How long? 
	 * @param string $seed Just some junk for addtional randomization
	 *
	 * @return mixed requested string 
	 */
	public function getRandom($type, $length = 10, $seed = 'z')
	{
		$mctime = microtime();
		$text = md5($mctime . base_convert(mt_rand(150, 500), 10, 36) . $seed);

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
