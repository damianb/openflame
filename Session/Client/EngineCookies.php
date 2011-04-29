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

namespace OpenFlame\Framework\Session\Client;
use \OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Session Client-side identification,
 * 		Hooks into the headers and input system that the framework has available
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class EngineCookies implements EngineInterface
{
	/*
	 * Holds all configuration options
	 */
	protected $options = array();

	const SID = 'sid';
	const UID = 'uid';
	const ALK = 'alk';

	/**
	 * Set options
	 * @param array - Key/value pair array for all client-id level config options
	 */
	public function setOptions($options)
	{
		$this->options['cookie.path'] = isset($options['cookie.path']) ? (string) $options['cookie.path'] : '/';
		$this->options['cookie.domain'] = isset($options['cookie.domain']) ? (string) $options['cookie.domain'] : $_SERVER['HTTP_HOST'];
		$this->options['cookie.secure'] = isset($options['cookie.secure']) ? true : false;
		$this->options['cookie.expire'] = isset($options['cookie.expire']) ? (int) $options['cookie.expire'] : 0;
		$this->options['cookie.prefix'] = isset($options['cookie.prefix']) ? (string) $options['cookie.prefix'] : '';
	}

	/**
	 * Get params as they were accepted from the client
	 * @return array - Structure: 'sid' => '', 'uid' => '', 'alk' => '' 
	 */
	public function getParams()
	{
		$input = Core::getObject('input');

		return array(
			'sid' 	=> (string) $input->getInput('COOKIE::' . $this->options['cookie.prefix'] . self::SID, ''),
			'uid' 	=> (string) $input->getInput('COOKIE::' . $this->options['cookie.prefix'] . self::UID, ''),
			'alk'	=> (string) $input->getInput('COOKIE::' . $this->options['cookie.prefix'] . self::ALK, ''),
		);
	}

	/**
	 * Set params to be stored by the client
	 * @param array - Structure: 'sid' => '', 'uid' => '', 'autologinkey' => ''
	 * @return void
	 */
	public function setParams($params)
	{
		if(isset($params['sid']) || isset($params['uid']) || isset($params['autologinkey']))
		{
			$header = Core::getObject('header');
			$cookie = $header->loadSubmodule('Cookie');
			$expire = ($this->options['cookie.expire'] != 0) ?  time() + $this->options['cookie.expire'] : 0;

			$cookie->setCookieDomain($this->options['cookie.domain'])
				->setCookiePath($this->options['cookie.path'])
				->setCookiePrefix($this->options['cookie.prefix'])
				->setCookieSecure($this->options['cookie.secure']);
		}

		if(isset($params['sid']))
		{
			$cookie->setCookie(self::SID)->setCookieValue($params['sid']);
		}

		if(isset($params['uid']))
		{
			$cookie->setCookie('uid')->setCookieValue($params['uid']);
		}

		if(isset($params['autologinkey']))
		{
			$cookie->setCookie('al')->setCookieValue($params['autologinkey']);
		}
	}
}
