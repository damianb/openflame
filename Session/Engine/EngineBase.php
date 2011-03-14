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
	 * Set cookie property method prefix
	 */
	const SET_COOKIE_METHOD = 'setCookie';

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
	 * Our setters
	 *
	 * @param string - Method name
	 * @param array - arguements
	
	 */
	public function __call($name, $args)
	{
		// Provide for setCookie* methods
		if(stripos($name, self::SET_COOKIE_METHOD) === 0)
		{
			$method = strtolower(substr($name, strlen(self::SET_COOKIE_METHOD)));
			$this->cfg['cookie.' . $method] = is_numeric($args[0]) ? (int) $args[0] : (string) $args[0];
		}

		return $this;
	}

	/*
	 * Start our session
	 */
	public function start()
	{
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
