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

if (!defined('OpenFlame\\ROOT_PATH')) exit;

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
	 * @var \OpenFlame\Framework\Session\Autologin\EngineInterface
	 */
	protected $autologinEngine;

	/*
	 *  @var user ID
	 */
	public $uid = '';

	/*
	 *  @var autlogin key
	 */
	public $alk = '';

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

	/*
	 * @var Is the session authenticated (i.e. logged in)
	 */
	public $authenticated = false;

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
	 * Sets the Session Autologin engine
	 * @param \OpenFlame\Framework\Session\Client\EngineInterface - The Session engine to use.
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setAutologinEngine(\OpenFlame\Framework\Session\Autologin\EngineInterface $engine)
	{
		$this->autologinEngine = $engine;

		return $this;
	}

	/**
	 * Sets the session storage engine to be used
	 * @param array - Options to feed the engine
	 * @return \OpenFlame\Framework\Session\Driver - Provides a fluent interface.
	 */
	public function setOptions($options)
	{
		// Copy the array in here
		$this->options = $options;

		// Now do some basic validation
		$this->options['session.expiretime'] = isset($options['session.expiretime']) ?
			(int) $options['session.expiretime'] : 3600;

		$this->options['session.ipvallevel'] = (isset($options['session.ipvallevel']) &&
			$options['session.ipvallevel'] > 0 && $options['session.ipvallevel'] < 5) ?
			(int) $options['session.ipvallevel'] : 0;

		$this->options['session.loginsid'] = (isset($options['session.loginsid'])) ?
			(bool) $options['session.loginsid'] : true;

		$this->options['session.trackguest'] = (isset($options['session.trackguest'])) ?
			(bool) $options['session.trackguest'] : true;

		// These come after the validations above in case the drivers want to use them.
		$this->storageEngine->init($this->options);
		$this->clientEngine->setOptions($this->options);

		if (is_object($this->autologinEngine))
		{
			$this->autologinEngine->setOptions($this->options);
		}

		return $this;
	}

	/**
	 * Start the session
	 * @return void
	 */
	public function start()
	{
		$dispatcher = Core::getObject('dispatcher');

		$now = time();
		$paramsToSend = array();

		// Grab the data from our client id
		$params = $this->clientEngine->getParams();
		$sid = $params['sid'];
		$uid = $params['uid'];
		$alk = $params['alk'];

		$this->pullIPAddress();
		$this->pullIPPartial();

		// Our flag to make the logic flow a bit nicer
		$valid = false;
		$al = false;

		// Let's see if they have a session first
		if ($this->storageEngine->loadSession($sid))
		{
			list($this->data, $this->fingerprint, $exp, $this->uid, $this->authenticated) =
				$this->storageEngine->loadData();

			// Validate it / do autologin process
			if ($now < $exp)
			{
				$valid = true;
			}
		}

		// Now that we know they do not have a valid session, we need to check
		// if they are presenting autologin cookies
		if (!$valid && is_object($this->autologinEngine))
		{
			$this->uid = $this->autologinEngine->lookup($alk);

			// This MUST be a strict compare
			if ($uid === $this->uid)
			{
				// They should get a new session now
				$this->sid = $this->storageEngine->newSession();
				$valid = $al = true;

				$seeder = new \OpenFlame\Framework\Security\Seeder();
				$this->alk = $seeder->buildRandomString(22, '', '0123456789abcdefghijklmnopqrstuvwxyz');
				$this->autologinEngine->store($this->uid, $this->alk);

				// In case we want to manipulate the session data when autologin is successful.
				$event = $dispatcher->triggerUntilBreak(\OpenFlame\Framework\Event\Instance::newEvent('session.autologin')
					->setData(array('uid'=>$this->uid,'data'=>$this->data)));

				if ($event->countReturns() > 1)
				{
					throw new \LogicException("Too many responses to the 'session.autologin' event.");
				}

				$returns = $event->getReturns();
				if (!is_array($returns))
				{
					$returns = array();
				}
				$this->data = array_merge($this->data, $returns);

				$paramsToSend['uid'] = $this->uid;
				$paramsToSend['alk'] = $this->alk;
			}
		}

		// Valid up to this point? not for long
		if ($valid)
		{
			$fingerprint = $this->makeFingerprint();

			if ($fingerprint == $this->fingerprint)
			{
				$dispatcher = Core::getObject('dispatcher');

				$event = $dispatcher->triggerUntilBreak(\OpenFlame\Framework\Event\Instance::newEvent('session.get')
					->setData(array('useruid'=>$this->uid)));

				if ($event->countReturns() > 1)
				{
					throw new \LogicException("Too many responses to the 'session.get' event.");
				}

				// If it's not an array, something is not right here.
				$returns = $event->getReturns();
				if (!is_array($returns))
				{
					$returns = array();
				}

				// Just in case
				if (!is_array($this->data))
				{
					$this->data = array();
				}

				$this->data = array_merge($returns, $this->data);

				if (empty($this->sid))
				{
					$this->sid = $sid;
				}
			}
			else
			{
				$valid = false;
			}
		}

		if (!$valid)
		{
			$this->authenticated = false;
			$this->getDefaultData();
		}

		// Depending on if we are tracking guests or not, we must either delete
		// the session or createa  new one.
		if (!$valid && !$this->authenticated && !$this->options['session.trackguest'])
		{
			$this->storageEngine->deleteSession();
			$this->sid = '';
		}
		else if (!$valid && !$this->authenticated && $this->options['session.trackguest'])
		{
			$this->sid = $this->storageEngine->newSession(true);
		}

		if ($this->options['session.trackguest'] || $this->authenticated)
		{
			$paramsToSend['sid'] = $this->sid;

			// Make our client engine aware
			$this->clientEngine->setParams($paramsToSend);
			$this->expireTime = $now + $this->options['session.expiretime'];
		}
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
		$paramsToSend = array();

		$event = $dispatcher->triggerUntilBreak(\OpenFlame\Framework\Event\Instance::newEvent('session.login')
			->setData(array(
				'username'	=> $username,
				'password'	=> $password,
				'autologin'	=> (bool) $autologin,
				'flags'		=> $flags,
			))
		);

		if ($event->countReturns() > 1)
		{
			throw new LogicException("Too many responses to the 'session.login' event.");
		}

		$result = $event->getReturns();

		// Fill the data upon successful login
		if ($result['successful'])
		{
			$this->data = $result['data'];
			$this->uid = $result['uid'];

			if ($this->options['session.loginsid'])
			{
				$this->sid = $this->storageEngine->newSession(true);
			}

			if (is_object($this->autologinEngine) &&
				isset($result['autologin']) &&
				$result['autologin'] == true )
			{
				$seeder = new \OpenFlame\Framework\Security\Seeder();
				$this->alk = $seeder->buildRandomString(10, '', '0123456789abcdefghijklmnopqrstuvwxyz');
				$this->autologinEngine->store($this->uid, $this->alk);

				$paramsToSend['uid'] = $this->uid;
				$paramsToSend['alk'] = $this->alk;
			}

			$this->authenticated = true;
		}

		// If we set for the SID to change when we log in, do so here.
		if ($this->options['session.trackguest'] || $result['successful'])
		{
			$this->expireTime = time() + $this->options['session.expiretime'];
			$paramsToSend['sid'] = $this->sid;

			$this->clientEngine->setParams($paramsToSend);
		}
	}

	/**
	 * Kill the session
	 * @return void
	 */
	public function kill()
	{
		$dispatcher = Core::getObject('dispatcher');
		$this->sid = ($this->options['session.trackguest']) ? $this->storageEngine->newSession(true) : '';

		if ($this->alk)
		{
			$this->autologinEngine->lookup($this->alk);
		}

		$this->getDefaultData();

		if ($this->options['session.trackguest'])
		{
			$this->clientEngine->setParams(array(
				'sid' => $this->sid,
				'uid' => '',
				'alk' => '',
			));
		}
	}

	/**
	 * Commit the session data, should be called at the end of execution
	 * @return void
	 */
	public function commit()
	{
		if (!empty($this->sid))
		{
			$this->storageEngine->storeData(array(
				$this->data,
				$this->fingerprint,
				$this->expireTime,
				$this->uid,
				$this->authenticated,
			));
		}
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
	protected function getDefaultData()
	{
		$dispatcher = Core::getObject('dispatcher');

		$event = $dispatcher->triggerUntilBreak(\OpenFlame\Framework\Event\Instance::newEvent('session.default'));

		if ($event->countReturns() > 1)
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

		if (!$ip->getWasSet() || !filter_var($ip, FILTER_VALIDATE_IP))
		{
			if (!$xip->getWasSet() || !filter_var($xip, FILTER_VALIDATE_IP))
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
		if (empty($this->ipAddr))
		{
			$this->pullIPAddress();
		}

		if (strpos($this->ipAddr, ':'))
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
			$this->ipAddrPartial = implode('.', array_slice(explode('.', $this->ipAddr), 0, $this->options['session.ipvallevel']));
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
