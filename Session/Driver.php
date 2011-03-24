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
	 * @var IP validation level 
	 */
	protected $ipValLvl = 3;

	/*
	 * @var cookie data
	 */
	protected $cookieData = array();

	/*
	 * @var cookie name
	 */
	protected $cookieName = '';

	/*
	 * @var session expiry (seconds) - defaults to an hour 
	 */
	protected $sessionExpiry = 3600;

	/*
	 * @var http user agent
	 */
	protected $useragent = '';

	/*
	 * @var rand seed
	 */
	protected $randSeed = 'O';

	/*
	 * @var fingerprint
	 */
	protected $fingerprint = '';

	/*
	 * Cookie base names
	 */
	const SID_COOKIE	= '_sid';
	const UID_COOKIE	= '_u';
	const AL_COOKIE		= '_a';

	/**
	 * Get the session storage engine currently in use.
	 * @return \OpenFlame\Framework\Session\Storage\* - The session storage engine in use.
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

	/**
	 * Set the cookie name prefix (not required)
	 *
	 * @var string - Name of the cookie
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setCookieName($name)
	{
		$this->cookieName = (string) $name;
		$this->engine->setCookieName((string) $name);

		return $this;
	}

	/**
	 * Set the session expiry 
	 *
	 * @var int - Time in seconds before the session exipires
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setSessionExpiry($time)
	{
		$this->sessionExpiry = (int) $time;

		return $this;
	}

	/**
	 * Set user agent 
	 *
	 * @var string - user agent
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setUseragent($useragent)
	{
		$this->useragent = (string) $useragent;

		return $this;
	}

	/**
	 * Set the random seed (not required)
	 *
	 * @var string - random string
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setRandSeed($seed)
	{
		$this->randSeed = (string) $seed;

		return $this;
	}

	/*
	 * Set an IP
	 *
	 * @param string cleaned IP address
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setIp($ip)
	{
		$this->ipAddr = $ip;
		$this->ipPartial = (strpos($ip, '.') && $this->ipValLvl > 0) ? implode('.', array_slice(explode('.', $ip), 0, $this->ipValLvl)) : $ip;

		return $this;
	}

	/*
	 * Set the IP validation level
	 *
	 * @param int - 1-4, makes no differnece on IPv6
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setValLevel($level)
	{
		$this->ipValLvl = ($level >= 0 && $level <= 4) ? $level : 3;

		return $this;
	}

	/**
	 * Init the session handler (called after construction)
	 *
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function init()
	{
		// Get the validated cookie
		$this->cookieData = array(
			self::SID_COOKIE	=> (preg_match($this->engine->sidRegExp, $_COOKIE[$this->cookieName . self::SID_COOKIE]) == 1) ? $_COOKIE[$this->cookieName . self::SID_COOKIE] : '',
			self::UID_COOKIE	=> (preg_match($this->engine->uidRegExp, $_COOKIE[$this->cookieName . self::UID_COOKIE]) == 1) ? $_COOKIE[$this->cookieName . self::UID_COOKIE] : '',
			self::AL_COOKIE		=> (preg_match($this->engine->alRegExp, $_COOKIE[$this->cookieName . self::AL_COOKIE]) == 1) ? $_COOKIE[$this->cookieName . self::AL_COOKIE] : '',
		);

		// Call anything that needs to be initialized in the engine
		$this->engine->init(
			$this->cookieData[self::SID_COOKIE],
			$this->cookieData[self::UID_COOKIE],
			$this->cookieData[self::AL_COOKIE],
		);

		// Let the session handler have this so we can be sure the rand seed is
		// the same each time
		$this->engine->setRandSeed($this->randSeed);

		return $this;
	}

	/*
	 * Start the session
	 *
	 * @return void
	 */
	public function start()
	{
		$valid = false;

		// Check for a returning click
		if ($this->cookieData[self::SID_COOKIE])
		{
			// Check to see if the currrent fingerprint matches the one from the
			// previous request. Then check to see if the session expired.
			if(	$this->createFingerprint() === $this->engine->getFingerprint() &&
				(time() > $this->engine->getLastClickTime() + $this->sessionExpiry)
			{
				$this->data = $this->engine->getData();
				$valid = true;
			}
		}

		// If they are not returning on a click, check their autologin
		if (!$valid && $this->engine->checkAutoLogin())
		{
			// create the new session
			$this->create();
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

	/*
	 * Create a session
	 *
	 * @return void
	 */
	public function create()
	{
		$this->engine->setFingerprint($this->createFingerprint());
	}

	/*
	 * Kill the session (logout)
	 *
	 * @return void
	 */
	public function kill()
	{
	}

	/*
	 * Login
	 *
	 * @param string - Username
	 * @param string - Password
	 * @param bool - autologin box checked?
	 * @return void
	 */
	public function login($username, $password, $autologin = false)
	{
		// @todo provide some sort of hook for the application, waiting on the
		// event handler
	}

	/*
	 * Set the cookie
	 */
	public function setCookie()
	{
	}

	/*
	 * Garbage collection
	 * still working on exact logistics
	 */
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
			$this->fingerprint = hash('sha1', $this->useragent . $this->ipPartial . $this->engine->getRandSeed());
		}

		return $this->fingerprint;
	}
}
