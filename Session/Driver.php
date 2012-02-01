<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  session
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Session;
use \emberlabs\openflame\Core\DependencyInjector;
use \emberlabs\openflame\Event\Instance as Event;

/**
 * OpenFlame Framework - Session Handler
 * 		Stores data associated with returning visits for the duration of a session.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class Driver
{
	/*
	 * @var string ip - Post-validation IP address
	 */
	public $ip = '';

	/*
	 * @var string ua - HTML-sanitized User Agent
	 */
	public $ua = '';

	/*
	 * @var array data - The public session data for easy access
	 */
	public $data = array();

	/*
	 * @var string sid - Post-validation Session ID
	 */
	private $sid = '';

	/*
	 * @var storage engine
	 */
	private $storage;

	/*
	 * @var client engine
	 */
	private $client;

	/*
	 * @var array options
	 */
	private $options = array();

	/*
	 * Sets the session storage engine to be used
	 * @param \emberlabs\openflame\Session\Storage\EngineInterface - The Session engine to use.
	 * @return \emberlabs\openflame\Session\Driver - Provides a fluent interface.
	 */
	public function setStorageEngine(\emberlabs\openflame\Session\Storage\EngineInterface $e)
	{
		$this->storage = $e;

		return $this;
	}

	/**
	 * Sets the Session Client-side identification engine
	 * @param \emberlabs\openflame\Session\Client\EngineInterface - The Session engine to use.
	 * @return \emberlabs\openflame\Session\Driver - Provides a fluent interface.
	 */
	public function setClientEngine(\emberlabs\openflame\Session\Client\EngineInterface $e)
	{
		$this->client = $e;

		return $this;
	}

	/*
	 * Set options for the session and its related engines
	 * @param array options - Associatvie array of options
	 * @return \emberlabs\openflame\Session\Driver - Provides a fluent interface.
	 */
	public function setOptions($options)
	{
		$defaults = array(
			'session.expire'	=> 3600,
			'session.ipval'		=> 3,
		);

		$options = array_merge($defaults, $options);

		$this->storage->init($options);
		$this->client->init($options);

		$this->options = $options;
		return $this;
	}

	/*
	 * Start the session
	 * @return void
	 */
	public function start()
	{
		$now = time();
		$input = DependencyInjector::grab('input');

		// The SID the browser claims to have
		$sid = $this->client->getSID();

		// We're going to have this even if we do not have a session.
		$this->ip = $this->extractIp();
		$this->ua = $input->getInput('SERVER::HTTP_USER_AGENT', '')->getClean();

		// Are they presenting a session id?
		if (!empty($sid))
		{
			// Data from the session from the session presented
			$data = $this->storage->load($sid);
			$clear = true;

			// Check for size in data and required fields
			if (sizeof($data) && isset($data['_lastclick']) && isset($data['_fingerprint']) && isset($data['_salt']))
			{
				$fingerprint = $this->makeFingerprint($data['_salt']);

				if (($data['_lastclick'] + $this->options['session.expire']) > $now && $fingerprint == $data['_fingerprint'])
				{
					// It validates.
					$this->data = $data;
					$this->sid = $sid;
					$data['_lastclick'] = $now;

					// Disarm
					$clear = false;
				}
			}

			// Clear our data should something not validate
			if ($clear)
			{
				$sid = '';
				$this->data = $data = array();
			}

			// Set our SID, whatever it may be
			$this->client->setSID($sid);
		}
	}

	/*
	 * Kill the session
	 * @return void
	 */
	public function kill()
	{
		$this->storage->purge($this->sid);
		$this->client->setSID('');

		$this->sid = '';
		$this->data = array();
	}

	/*
	 * Commit session data - Should be called after you're done modifying session data and before page output.
	 * @return void
	 */
	public function commit()
	{
		if (sizeof($this->data))
		{
			$sid = $this->getSid();

			if (!isset($this->data['_salt']))
			{
				$seeder = DependencyInjector::grab('seeder');
				$this->data['_salt'] = $seeder->buildRandomString(10);
			}

			$this->data['_fingerprint'] = $this->makeFingerprint($this->data['_salt']);

			if (!isset($data['_lastclick']))
			{
				$this->data['_lastclick'] = time();
			}

			$this->storage->store($sid, $this->data);
			$this->client->setSID($sid);
		}
	}

	/*
	 * Get the SID
	 * Unlike any other piece of data, this version of session handling is quite the couch-potato (i.e. lazy). It wont actually generate an SID until it either needs it or the application needs it.
	 * @return string - sid
	 */
	public function getSid()
	{
		if (empty($this->sid) && sizeof($this->data))
		{
			$seeder = DependencyInjector::grab('seeder');
			$this->sid = $seeder->buildRandomString(32);
		}

		return $this->sid;
	}

	/*
	 * Is the session being tracked?
	 * @return boolean
	 */
	public function isTracked()
	{
		return sizeof($this->data) ? true : false;
	}

	/*
	 * Run Session-based garbage collectors
	 * @param \emberlabs\openflame\Event\Instance $e - Event instance (so this can be used as a listener)
	 * @return void
	 */
	public function gc(Event $e = NULL)
	{
		$this->storage->gc($e);
	}

	/*
	 * Make the fingerprint
	 * @return string - Hash of the fingerprint
	 */
	private function makeFingerprint($salt)
	{
		if (strpos($this->ip, ':') !== false)
		{
			// IPv6
			// We're assuming they are going to keep the same IP address
			$ipPartial = $this->ip;
		}
		else
		{
			// Slice up the IP into our validation level
			$ipPartial = implode('.', array_slice(explode('.', $this->ip), 0, $this->options['session.ipval']));
		}

		return md5($ipPartial . $this->ua . $salt);
	}

	/*
	 * Extract IP from SERVER
	 * @return string - good IP
	 */
	private function extractIp()
	{
		$input = DependencyInjector::grab('input');
		$cleanip = '';

		$ip = $input->getInput('SERVER::REMOTE_ADDR', '127.0.0.1');
		$xip = $input->getInput('SERVER::HTTP_X_REMOTE_ADDR', '127.0.0.1');

		if (!$ip->getWasSet() || !filter_var($ip, FILTER_VALIDATE_IP))
		{
			if (!$xip->getWasSet() || !filter_var($xip, FILTER_VALIDATE_IP))
			{
				$cleanIp = '0.0.0.0';
			}
			else
			{
				$cleanIp = $xip->getClean();
			}
		}
		else
		{
			$cleanIp = $ip->getClean();
		}

		return $cleanIp;
	}
}
