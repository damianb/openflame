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
use \OpenFlame\Framework\Core;
use OpenFlame\Framework\Dependency\Injector;

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
	 * @var injector
	 */
	private $injector;

	/*
	 * Initialize the engine
	 * @param array options - Associative array of options
	 * @return void
	 */
	public function init(&$options)
	{
		$this->injector = Injector::getInstance();
		$input = $this->injector->get('input');

		// Sanity check, we should not have cookie expirations less than our 
		// session, otherwise the driver will take a shit over the filesystem
		// until garbage collection comes around
		$options['cookie.expire'] = isset($options['cookie.expire']) ?
			$options['cookie.expire'] : 0;

		// Fallback on SERVER_NAME
		$options['cookie.domain'] = isset($options['cookie.domain']) ? 
			(string) $options['cookie.domain'] : $input->getInput('SERVER::SERVER_NAME', '')->getClean();

		// Fallback on /
		$options['cookie.path'] = isset($options['cookie.path']) ?
			(string) $options['cookie.path'] : '/';

		// Cast these properly
		$options['cookie.name'] = isset($options['cookie.name']) ? (string) $options['cookie.name'] : 'sid';
		$options['cookie.secure'] = isset($options['cookie.secure']) ? (boolean) $options['cookie.secure'] : false;

		$this->options = $options;
	}

	/*
	 * Get the SID of the current visitor
	 * @return string - Session ID 
	 */
	public function getSID()
	{
		$input = $this->injector->get('input');

		return $input->getInput('COOKIE::' . $this->options['cookie.name'], '')->getClean();
	}

	/*
	 * Set an SID
	 * @param string sid - Session ID to set
	 * @return void
	 */
	public function setSID($sid)
	{
		$cookie = $this->injector->get('cookie');

		$cookie->setCookieDomain($this->options['cookie.domain'])
			->setCookiePath($this->options['cookie.path'])
			->setCookiePrefix('');

		if ($this->options['cookie.secure'])
		{
			$cookie->enableSecureCookies();
		}

		$cookie->setCookie($this->options['cookie.name'])->setCookieValue($sid);
	}
}
