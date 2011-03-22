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
	protected $engine;

	/*
	 * @var validated IP address
	 */
	protected $ipAddr;

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

	public function init()
	{
		// Call anything that needs to be initialized in the engine
		$this->engine->init();

		return $this;
	}

	public function start()
	{
		if ($this->engine->returning())
		{
			$sessionData = $this->engine->getData();
		}
		else if ($this->engine->checkAutoLogin())
		{
		}
		else
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
}
