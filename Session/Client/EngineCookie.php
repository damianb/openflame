<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  session
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Session\Client;
use \OpenFlame\Framework\Core\DependencyInjector;

/**
 * OpenFlame Framework - Session Client-side identification,
 * 		Sessions client engine prototype
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class EngineCookie implements EngineInterface
{
	/*
	 * @var Cookie options
	 */
	private $options;

	/*
	 * @var the data read from the cookie (So we don't have to end up taking
	 * inputs twice.)
	 */
	public $readCookie = '';

	/*
	 * Initialize the engine
	 * @param array options - Associative array of options
	 * @return void
	 */
	public function init(array &$options)
	{
		$input = DependencyInjector::grab('input');

		$defaults = array(
			'cookie.expire'	=> 0,
			'cookie.domain'	=> $input->getInput('SERVER::SERVER_NAME', '')->getClean(),
			'cookie.path'	=> '/',
			'cookie.name'	=> 'sid',
			'cookie.prefix'	=> '',
			'cookie.secure'	=> false,
		);

		$this->options = array_merge($defaults, $options);

		// Set up our cookie
		$cookie = DependencyInjector::grab('cookie');

		$cookie->setCookieDomain($this->options['cookie.domain'])
			->setCookiePath($this->options['cookie.path'])
			->setCookiePrefix($this->options['cookie.prefix']);

		if ($this->options['cookie.secure'])
		{
			$cookie->enableSecureCookies();
		}
	}

	/*
	 * Get the SID of the current visitor
	 * @return string - Session ID
	 */
	public function getSID()
	{
		if (empty($this->readCookie))
		{
			$input = DependencyInjector::grab('input');
			$fullName = $this->options['cookie.prefix'] . $this->options['cookie.name'];

			$this->readCookie = $input->getInput("COOKIE::$fullName", '')->getClean();
		}

		return $this->readCookie;
	}

	/*
	 * Set an SID
	 * @param string sid - Session ID to set
	 * @return void
	 */
	public function setSID($sid)
	{
		if ($sid != $this->getSID())
		{
			$cookie = DependencyInjector::grab('cookie');

			if (empty($sid))
			{
				$cookie->expireCookie($this->options['cookie.name']);
			}
			else
			{
				$cookie->setCookie($this->options['cookie.name'])
					->setCookieValue($sid)
					->setExpireTime($this->options['cookie.expire']);
			}
		}
	}
}
