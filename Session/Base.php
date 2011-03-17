<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Session;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Session Handler Base
 * 	     The base class for the session handler. 
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
abstract class Base
{
	/*
	 * @var - Array of user information
	 */
	public $data = array();

	/*
	 * @var - Session validation information
	 *
	 * This property is private by design: this class should be the /only/ one
	 * to modify or read it.
	 */
	private $val = array();

	/*
	 * @var - Configuration data surrounding the session
	 */
	private $cfg = array();

	/*
	 * @var - has init been called?
	 */
	private $initCalled = false;

	/*
	 * Init
	 * This will lazily be called when it's needed
	 *
	 * @return void
	 */
	private function init()
	{
		if(!$this->initCalled)
		{
			$this->initCalled = true;
			
			// Set some defaults
			$this->cfg = array(
				'session.name'		=> 'PHPSESSID',
				'session.savepath'	=> '',
				'session.refcheck'	=> '',

				'cookie.life'		=> 0,
				'cookie.path'		=> '/',
				'cookie.domain'		=> $_SERVER['HTTP_HOST'],
				'cookie.secure'		=> false,
				'cookie.httponly'	=> true,
			);
		}
	}

	/*
	 * Applies session and cookie settings prior to calling session_start()
	 *
	 * @return void
	 */
	private function applySettings()
	{
		// One more time                                  ...we gonna celebrate
		$this->init();

		// Set the basic session information
		session_name($this->cfg['session.name'));
		session_save_path($this->cfg['session.savepath'));

		// Set cookie the params
		session_set_cookie_params(
			$this->cfg['cookie.lifetime'], 
			$this->cfg['cookie.path'], 
			$this->cfg['cookie.domain'], 
			$this->cfg['cookie.secure'], 
			$this->cfg['cookie.httponly']);

		ini_set('session.referer_check', $this->cfg['session.refcheck']);
	}

	/*
	 * Sets a cookie option
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function setCookieOption($opt, $val)
	{
		$this->init();
		$cfg['cookie.' . $opt] = $val;

		return $this;
	}

	/*
	 * Sets the session's save path
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function setSessionSavepath($path)
	{
		$this->init();
		$cfg['session.savepath'] = $val;

		return $this;
	}

	/*
	 * Sets the session's save path
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function setSessionName($name)
	{
		$this->init();
		$cfg['session.name'] = $val;

		return $this;
	}

	/*
	 * Sets the session's save path
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function setSessionRefcheck($refCheck)
	{
		$this->init();
		$cfg['session.refcheck'] = $refCheck;

		return $this;
	}

	/*
	 * Start the session 
	 * Starts a session from a privious page load and validates all data
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function start()
	{
		$this->applySettings();

		return $this;
	}

	/*
	 * Session create
	 * Regenerates session id and creates a new session if one does not exist
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function create()
	{
		return $this;
	}

	/*
	 * Login
	 * Gatway to login
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function login()
	{
		return $this
	}

	/*
	 * Kill session
	 * For logging out
	 *
	 * @return OpenFlame\Framework\Session\Base - Provides a fluent interface.
	 */
	public function kill()
	{
		return $this;
	}
}
