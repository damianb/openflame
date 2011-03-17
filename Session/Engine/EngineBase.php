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

namespace OpenFlame\Framework\Session\Engine;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - File-based cache engine base class,
 * 		Cache engine prototype, provides some common methods for all file-based engines to use.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
abstract class EngineBase
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
	 * Pseudo Constructor
	 * Must be called right after an instance is created
	 * 
	 * @return OpenFlame\Framework\Session\Engine\EngineBase - Provides a fluent interface.
	 */
	public function init()
	{
		$this->cfg = array(
			'cookie.lifetime'	=> 0,
			'cookie.path'		=> '/',
			'cookie.domain'		=> $_SERVER['HTTP_HOST'],
			'cookie.secure'		=> true,
		);
	}

	/*
	 * Set cookie name
	 *
	 * @param string - cookie name
	 * @return OpenFlame\Framework\Session\Engine\EngineBase - Provides a fluent interface.
	 */
	public function setCookieName($name)
	{
		$this->cfg['cookie.name'] = (string) $name;
		return $this;
	}

	/*
	 * Set cookie life
	 *
	 * @param int - cookie life (in seconds)
	 * @return OpenFlame\Framework\Session\Engine\EngineBase - Provides a fluent interface.
	 */
	public function setCookieLife($life)
	{
		$this->cfg['cookie.life'] = (int) $life;
		return $this;
	}

	/*
	 * Set cookie path
	 *
	 * @param string - cookie path
	 * @return OpenFlame\Framework\Session\Engine\EngineBase - Provides a fluent interface.
	 */
	public function setCookiePath($path)
	{
		$this->cfg['cookie.path'] = (string) $path;
		return $this;
	}

	/*
	 * Set cookie domain
	 *
	 * @param string - cookie domain
	 * @return OpenFlame\Framework\Session\Engine\EngineBase - Provides a fluent interface.
	 */
	public function setCookieDomain($domain)
	{
		$this->cfg['cookie.domain'] = (string) $domain;
		return $this;
	}

	/*
	 * Set cookie secure flag
	 *
	 * @param bool - Should the cookie be secure?
	 * @return OpenFlame\Framework\Session\Engine\EngineBase - Provides a fluent interface.
	 */
	public function setCookieSecure($secure)
	{
		$this->cfg['cookie.secure'] = (bool) $secure;
		return $this;
	}

	/*
	 * Start our session
	 */
	public function start()
	{
		session_name($this->cfg['cookie.name']);

		// Set the params
		session_set_cookie_params(
			$this->cfg['cookie.lifetime'],
			$this->cfg['cookie.path'],
			$this->cfg['cookie.domain'],
			$this->cfg['cookie.secure'],
			true
		);
	}
}
