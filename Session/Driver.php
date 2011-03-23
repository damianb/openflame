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
class Driver
{
	/*
	 * @var session data
	 */
	public $data = array();

	/*
	 * @var \OpenFlame\Framework\Session\Storage\EngineInterface
	 */
	protected $engine;

	/*
	 * @var current IP address
	 */
	protected $ipAddr = '';

	/*
	 * @var partial IP for comparison
	 */
	protected $ipPartial = '';

	/*
	 * @var cookie data
	 */
	protected $cookieData = array();

	/*
	 * @var cookie name
	 */
	protected $cookieName = '';

	/*
	 * @var fingerprint
	 */
	protected $fingerprint = '';

	/*
	 * @var session expiry (seconds) - defaults to an hour 
	 */
	protected $sessionExpiry = 3600;

	/*
	 * Cookie base names
	 */
	const SID_COOKIE	= '_sid';
	const UID_COOKIE	= '_u';
	const AL_COOKIE		= '_a';

	/**
	 * Get the session storage engine currently in use.
	 * @return \OpenFlame\Framework\Session\Storage\EngineInterface - The session storage engine in use.
	 */
	public function getEngine()
	{
		return $this->engine;
	}

	/**
	 * Dependency injection method, sets the session storage engine to be used
	 * @param \OpenFlame\Framework\Session\Storage\EngineInterface - The session storage engine to use.
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setEngine(\OpenFlame\Framework\Cache\Engine\EngineInterface $engine)
	{
		$this->engine = $engine;

		return $this;
	}

	public function setCookieName($name)
	{
		$this->cookieName = (string) $name;
	}

	public function setSessionExpiry($time)
	{
		$this->sessionExpiry = (int) $time;
	}

	public function init()
	{
		// Get the validated cookie
		$this->cookieData = array(
			self::SID_COOKIE	=> (preg_match($this->engine->sidRegExp, $_COOKIE[$this->cookieName . self::SID_COOKIE]) == 1) ? $_COOKIE[$this->cookieName . self::SID_COOKIE] : '',
			self::UID_COOKIE	=> (preg_match($this->engine->uidRegExp, $_COOKIE[$this->cookieName . self::UID_COOKIE]) == 1) ? $_COOKIE[$this->cookieName . self::UID_COOKIE] : '',
			self::AL_COOKIE		=> (preg_match($this->engine->alRegExp, $_COOKIE[$this->cookieName . self::AL_COOKIE]) == 1) ? $_COOKIE[$this->cookieName . self::AL_COOKIE] : '',
		);

		// Call anything that needs to be initialized in the engine
		$this->engine->init();

		return $this;
	}

	public function start()
	{
		$valid = false;
		$this->extractIp();

		// Check for a returning click
		if (!empty($this->cookieData[self::SID_COOKIE]))
		{
			// Check to see if the currrent fingerprint matches the one from the
			// previous request. Then check to see if the session expired.
			if(	$this->createFingerprint() === $this->engine->getFingerprint() &&
				($this->sessionExpiry + time()) > $this->engine->getSessionExpiry()
			)
			{
				$this->data = $this->engine->getData();
				$valid = true;
			}
		}

		// If they are not returning on a click, check their autologin
		if (!$valid && $this->engine->checkAutoLogin())
		{
			$this->data = $this->engine->getData();
			$valid = true;
		}

		// No autologin, no returning, new session
		if(!$valid)
		{
			// new session, create it
			$this->create();
		}
	}

	public function create()
	{
	}

	public function kill()
	{
	}

	public function login($username, $password, $autologin = false)
	{
	}

	public function setCookie()
	{
	}

	public function gc()
	{
		$this->engine->gc();
	}

	/*
	 * Create the user's fingerprint for validation
	 * This is set up to return and set a property to use in validation as well
	 * as setting the actual value.
	 * 
	 * @return string - sha1 of the user's fingerprint
	 */
	private function createFingerprint()
	{
		if(strlen($this->fingerprint) == 0)
		{
			$this->fingerprint = sha1($_SERVER['HTTP_USER_AGENT'] . $this->ipPartial);
		}

		return $this->fingerprint;
	}

	private function extractIp()
	{
	}
}
