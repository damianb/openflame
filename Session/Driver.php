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
	 * @var \OpenFlame\Framework\Session\Storage\EngineInterface
	 */
	protected $storageEngine;

	/*
	 * @var \OpenFlame\Framework\Session\Client\EngineInterface
	 */
	protected $clientEngine;

	/*
	 * @var session data
	 */
	public $data = array();

	/*
	 *  @var session id
	 */
	public $sid = '';

	/*
	 *  @var user ID
	 */
	public $uid = '';

	/*
	 *  @var autlogin key
	 */
	public $autologinKey = '';

	/**
	 * Sets the session storage engine to be used
	 * @param \OpenFlame\Framework\Session\Storage\EngineInterface - The Session engine to use.
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setStorageEngine(\OpenFlame\Framework\Session\Storage\EngineInterface $engine)
	{
		$this->storageEngine = $engine;

		return $this;
	}

	/**
	 * Sets the Session Client-side identification engine
	 * @param \OpenFlame\Framework\Session\Client\EngineInterface - The Session engine to use.
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setStorageEngine(\OpenFlame\Framework\Session\Client\EngineInterface $engine)
	{
		$this->clientEngine = $engine;

		return $this;
	}

	/**
	 * Sets the session storage engine to be used
	 * @param array - Options to feed the engine
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setEngineOptions($options)
	{
		$this->storageEngine->init($options);
		$this->clientEngine->setOptions($options);

		return $this;
	}

	/**
	 * Start the session 
	 * @return void
	 */
	public function start()
	{
		$this->clientEngine->onStart();
	}

	/**
	 * Login - Mainly gets handled by the application
	 *
	 * @param string - Username
	 * @param string - Password (still in plain text)
	 * @param boolean - Was the autologin box checked?
	 * @param mixed - Flags that will be passed to the application
	 * @return bool - true if logged in, false if not
	 */
	public function login($username, $password, $autologin = false, $flags = array())
	{
		$this->clientEngine->onLogin();
	}

	/**
	 * Start the session 
	 * @return void
	 */
	public function kill()
	{
		$this->clientEngine->onKill();
	}
}
