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
 * @uses Doctrine 1.2
 * @uses OfSession.php
 * @uses OfHash.php
 * @uses OfConfig.php
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
	 * @var array $updateQueue
	 *
	 * Values to be looped through when running updateUser()
	 */
	private $updateQueue = array();

	/**
	 * @var array $userRow
	 *
	 * Array that contains the raw user row from the database
	 */
	private $userRow = array();

	/**
	 * @var array $cookieData
	 *
	 * Contents of the cookie jar
	 */
	private $cookieData = array();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// call our initialize from OfSession
		$this->init();

		// Take two from the cookie jar and count the chocolate chips
		$cookieName = Of::config('session.cookie.name');
		$this->cookieData = array(
			'k'		=> isset($_COOKIE[$cookieName  . '_k']) ? preg_replace('#[^0-9a-f]#i', '', $_COOKIE[$cookieName  . '_k']) : '',
			'uid'	=> isset($_COOKIE[$cookieName  . '_uid']) ? preg_replace('#[^0-9]#', '', $_COOKIE[$cookieName  . '_uid']) : '',
		);
	}

	/**
	 * Authenticate user
	 *
	 * Authenticates user upon login. Must place validated ID in $this->userId
	 *
	 * @returns bool true if authenticated, false if failed
	 */
	protected function authenticateUser()
	{
		// Make sure there is something being posted to the page
		if(!empty($_POST['username']) && !empty($_POST['password']))
		{
			// grab user data
			$this->grabUserData('username', $_POST['username']);

			// OfHash
			if(!class_exists('OfHash'))
				include OF_ROOT . 'OfHash.php';

			$hasher = new OfHash(8, true);

			// The all important check...
			if($hasher->check($_POST['password'], $this->userRow['password']))
			{
				// Good ID now, transfer it over
				$this->userId = $this->userRow['userId'];

				// handle autologin
				if(isset($_REQUEST['autologin']) && Of::config('session.autologin'))
				{
					$key = $this->generateKey();

					$this->setCookie('k', $key);
					$this->setCookie('uid', $this->userId);

					$this->updateQueue['autoLogin'] = 1;
					$this->updateQueue['autoLoginKey'] = $key;
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Validate Auto Login
	 *
	 * Validates the Auto Login data. Places validated id in $this->userId
	 *
	 * @return bool true if passed, false if failed.
	 */
	protected function validateAutoLogin()
	{
		// Failure is instant when presented with a lack of cookies
		if(empty($this->cookieData['k']) || empty($this->cookieData['uid']))
			return false;

		// This is temporary while we use ::grabUserData()
		$this->userId = $this->cookieData['uid'];
		$this->grabUserData();

		if($this->userRow['autoLogin'] == 1 && $this->userRow['autoLoginKey'] == $this->cookieData['k'])
		{
			$key = $this->generateKey();

			$this->setCookie('k', $key);
			$this->setCookie('uid', $this->userId);

			$this->updateQueue['autoLoginKey'] = $key;

			return true;
		}
		else
		{
			$this->userId = 0;

			$newExp = $this->now - 30;
			$this->setCookie('k', '', $newExp);
			$this->setCookie('uid', '', $newExp);

			return false;
		}
	}

	/**
	 * Fills user data
	 *
	 * Places the data associated with the user in $this->userId inside the
	 * $this->data array. Switches $this->val['isLoggedIn'] to true.
	 *
	 * @return void
	 */
	protected function fillUserData()
	{
		if($this->userId > 0)
		{
			$this->grabUserData();

			$this->data = $this->userRow;
			$this->val['isLoggedIn'] = true;
		}
	}

	/**
	 * Update User
	 *
	 * This function will allow the extending class to run database updates
	 * on the user that is currently logged in.
	 *
	 * @return void
	 */
	protected function updateUser()
	{
		if(sizeof($this->updateQueue))
		{
			$query = Doctrine_Query::create()
				->update('Users u');

			foreach($this->updateQueue as $col => $val)
			{
				$this->data[$col] = $val;
				$query->set("u.{$col}", '?', $val);
			}

			$query->where('u.userId = ?', $this->userId)
				->execute();
		}
	}

	/**
	 * On Session Kill
	 *
	 * This function will allow the extending class to run code upon a session
	 * being killed at the session handler level.
	 *
	 * @return void
	 */
	protected function onSessionKill()
	{
		// Right now we are just clearing out the autologin on a logout
		Doctrine_Query::create()
			->update('Users u')
			->set('u.autoLogin', '?', 0)
			->set('u.autoLoginKey', '?', '00000000000000000000')
			->where('u.userId = ?', $this->userId)
			->execute();
	}

	/**
	 * Grab User data
	 *
	 * Grabs the user whos id is in $this->userId
	 *
	 * @param string $findByCol Column in the database to search (such as username)
	 * @param mixed $findByVal Value we are looking for
	 * @return void
	 */
	private function grabUserData($findByCol = '', $findByVal = '')
	{
		$query = Doctrine::getTable('Users')->createQuery('u');

		if(!empty($findByCol) && !empty($findByVal) && (!sizeof($this->userRow) || $this->userRow[$findByCol] != $findByVal))
		{
			$query->where("u.{$findByCol} = ?", $findByVal);
			$this->userRow = $query->fetchOne();
		}
		else if(!empty($this->userId) && (!sizeof($this->userRow) || $this->userRow['userId'] != $this->userId) )
		{
			$query->where('u.userId = ?', $this->userId);
			$this->userRow = $query->fetchOne();
		}
	}

	/**
	 * Generates a 20 character key for the session cookie
	 *
	 * @return string 20 character random hex string
	 */
	protected function generateKey()
	{
		return substr(md5(mt_rand(0, 20) . Of::config('website.salt')), 1, 20);
	}
}
