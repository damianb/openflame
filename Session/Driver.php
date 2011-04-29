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
use \OpenFlame\Framework\Event\Instance as Event;

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
	 *  @var user ID
	 */
	protected $uid = '';

	/*
	 *  @var autlogin key
	 */
	protected $alk = '';

	/*
	 *	@var fingerprint
	 */
	protected $fingerprint = '';

	/*
	 *	@var expireTime
	 */
	protected $expireTime = 0;

	/*
	 *	@var options
	 */
	protected $options = array();

	/*
	 * @var IP Partial 
	 */
	protected $ipAddrPartial = '';

	/*
	 * @var IP Address
	 */
	public $ipAddr = '';

	/*
	 * @var session data
	 */
	public $data = array();

	/*
	 *  @var session id
	 */
	public $sid = '';

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
	public function setClientIdEngine(\OpenFlame\Framework\Session\Client\EngineInterface $engine)
	{
		$this->clientEngine = $engine;

		return $this;
	}

	/**
	 * Sets the session storage engine to be used
	 * @param array - Options to feed the engine
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setOptions($options)
	{
		$this->options['expiretime'] = isset($options['expiretime']) ? 
			(int) $options['expiretime'] : 3600;
	
		$this->options['ipvallevel'] = (isset($options['ipvallevel']) && 
			$options['ipvallevel'] > 0 && $options['ipvallevel'] < 5) ? 
			(int) $options['ipvallevel'] : 0;

		$this->storageEngine->init(array_merge($options, $this->options));
		$this->clientEngine->setOptions(array_merge($options, $this->options));

		return $this;
	}

	/**
	 * Start the session 
	 * @return void
	 */
	public function start()
	{
		$now = time();
		// Grab the data from our client id
		$params = $this->clientEngine->getParams();
		$sid = $params['sid'];
		$uid = $params['uid'];
		$alk = $params['alk'];

		$this->pullIPAddress();
		$this->pullIPPartial();

		// Our flag to make the logic flow a bit nicer
		$valid = false;

		// Let's see if they have a session first
		if ($this->storageEngine->loadSession($sid))
		{
			list($this->data, $this->fingerprint, $exp, $this->uid, $this->alk) = $this->storageEngine->loadData();

			// Validate it / do autologin process
			if ($now < $exp)
			{
				$valid = true;
			}
			else
			{
				// Session is OVER, check for autologin
				if	($this->alk === $alk && $this->uid === $uid)
				{
					$this->sid = $this->storageEngine->newSession(true);
					$valid = true;
				}
			}
		}

		// Valid up to this point? not for long
		if($valid)
		{
			$fingerprint = $this->makeFingerprint();

			if($fingerprint == $this->fingerprint)
			{
				$dispatcher = Core::getObject('dispatcher');
	
				$event = $dispatcher->triggerUntilBreak(\OpenFlame\Framework\Event\Instance::newEvent('session.get')
					->setData(array(
						'userid'	=> $this->uid,
					))
				);

				if($event->countReturns() > 1)
				{
					throw new \LogicException("Too many responses to the 'session.get' event.");
				}

				// If it's not an array, something is not right here.
				$returns = $event->getReturns();
				if(!is_array($returns))
				{
					$returns = array();
				}

				if(!is_array($this->data))
				{
					$this->data = array();
				}

				$this->data = array_merge($returns, $this->data);

				if(empty($this->sid))
				{
					$this->sid = $sid;
				}
			}
			else
			{
				$valid = false;
			}
		}

		// If we do not have a valid session, create a new one
		if (!$valid)
		{
			$this->sid = $this->storageEngine->newSession(true);
			$this->defaultData();
		}

		// Make our client engine aware
		$this->clientEngine->setParams(array(
			'sid' => $this->sid,
			'uid' => $this->uid,
			'alk' => $this->alk,
		));

		$this->expireTime = $now + $this->options['expiretime'];
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
		$dispatcher = Core::getObject('dispatcher');

		$event = $dispatcher->triggerUntilBreak(\OpenFlame\Framework\Event\Instance::newEvent('session.login')
			->setData(array(
				'username'	=> $username,
				'password'	=> $password,
				'autologin'	=> (bool) $autologin,
				'flags'		=> $flags,
			))
		);

		if($event->countReturns() > 1)
		{
			throw new LogicException("Too many responses to the 'session.login' event.");
		}

		$result = $event->getReturns();

		if($result['successful'])
		{
			$this->sid = $this->storageEngine->newSession(true);
			$this->data = $result['data'];
			$this->alk = $result['alk'];
			$this->uid = $result['uid'];
		}

		$this->clientEngine->setParams(array(
			'sid'	=> $this->sid,
			'uid'	=> $this->uid,
			'alk'	=> $this->alk,
		));
	}

	/**
	 * Start the session 
	 * @return void
	 */
	public function kill()
	{
		$dispatcher = Core::getObject('dispatcher');
		$this->sid = $this->storageEngine->newSession(true);

		$this->defaultData();

		$this->clientEngine->setParams(array(
			'sid' => $this->sid,
			'uid' => '',
			'alk' => NULL,
		));
	}

	/**
	 * Commit the session data, should be called at the end of execution 
	 * @return void
	 */
	public function commit()
	{
		$this->storageEngine->storeData(array(
			$this->data, 
			$this->fingerprint, 
			$this->expireTime, 
			$this->uid, 
			$this->alk,
		));
	}

	/**
	 * Create fingerprint
	 * @return string
	 */
	protected function makeFingerprint()
	{
		// MD5 is faster, not going to have a sha1 running every page load
		return hash('md5', $this->ipAddrPartial . $_SERVER['HTTP_USER_AGENT']);
	}

	/*
	 * Fill $this->data with application-specific values
	 * @return void
	 * @throws LogicException
	 */
	protected function defaultData()
	{
		$dispatcher = Core::getObject('dispatcher');

		$event = $dispatcher->triggerUntilBreak(\OpenFlame\Framework\Event\Instance::newEvent('session.default'));

		if($event->countReturns() > 1)
		{
			throw new \LogicException("Too many responses to the 'session.default' event.");
		}

		$this->uid = '';
		$this->alk = NULL;
		$this->fingerprint = $this->makeFingerprint();

		$this->data = $event->getReturns();
	}

	/*
	 * Pull IP address
	 * Ported from legacy OfSession
	 * @return bool
	 */
	protected function pullIPAddress()
	{
		$input = Core::getObject('input');
		$ip		= $input->getInput('SERVER::REMOTE_ADDR', '127.0.0.1');
		$xip	= $input->getInput('SERVER::HTTP_X_REMOTE_ADDR', '127.0.0.1');

		if(!$ip->getWasSet() || !filter_var($ip, FILTER_VALIDATE_IP))
		{
			if(!$xip->getWasSet() || !filter_var($xip, FILTER_VALIDATE_IP))
			{
				$this->ipAddr = '0.0.0.0'; 
			}
			else
			{
				$this->ipAddr = $xip->getClean();
			}
		}
		else
		{
			$this->ipAddr = $ip->getClean();
		}
	}

	/*
	 * Pull a partial IP address
	 * @return bool
	 */
	protected function pullIPPartial()
	{
		if(empty($this->ipAddr))
		{
			$this->pullIPAddress();
		}

		if(strpos($this->ipAddr, ':'))
		{
			// IPv6
			// @TODO - Get partial validation working or continue to assume
			// everyone using IPv6 will have thier own IP for the duration of
			// the session
			$this->ipAddrPartial = $this->ipAddr;
		}
		else
		{
			// IPv4
			$this->ipAddrPartial = implode('.', array_slice(explode('.', $this->ipAddr), 0, $this->options['ipvallevel']));
		}
	}

	/*
	 * Garbage Collection
	 * Should be called periodically
	 */
	public function gc() 
	{ 
		$this->storageEngine->gc(); 
	}
}
