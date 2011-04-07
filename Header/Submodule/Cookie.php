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

namespace OpenFlame\Framework\Header\Submodule;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Header manager object
 * 	     Takes in and manages headers that should be sent upon page display.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Cookie implements \OpenFlame\Framework\Header\Submodule\SubmoduleInterface
{
	protected $manager;

	protected $now = 0;

	protected $cookies = array();

	protected $cookie_domain = '';

	protected $cookie_path = '';

	protected $cookie_prefix = 'opflame';

	protected $cookie_secure = false;

	protected $cookie_expire = 3600; // @todo find a sane default for cookie expire time o_O

	protected function __construct() { }

	public static function newInstance()
	{
		$self = new static();
		$self->setNowTime();
		return $self;
	}

	public function setManager(\OpenFlame\Framework\Header\Manager $manager)
	{
		$this->manager = $manager;
	}

	public function injectHeaders()
	{
		foreach($this->cookies as $cookie)
		{
			$this->manager->setHeader('Set-Cookie', $cookie->getFullCookieString());
		}
	}

	public function getNowTime()
	{
		return $this->now;
	}

	protected function setNowTime()
	{
		$this->now = time();

		return $this;
	}

	public function getCookieDomain()
	{
		return $this->cookie_domain;
	}

	public function setCookieDomain($cookie_domain)
	{
		// @todo validation
		$this->cookie_domain = (string) $cookie_domain;

		return $this;
	}

	public function getCookiePath()
	{
		return $this->cookie_path;
	}

	public function setCookiePath($cookie_path)
	{
		// @todo validation
		$this->cookie_path = (string) $cookie_path;

		return $this;
	}

	public function getCookiePrefix()
	{
		return $this->cookie_prefix;
	}

	public function setCookiePrefix($cookie_prefix)
	{
		$this->cookie_prefix = (string) $cookie_prefix;

		return $this;
	}

	public function getCookieSecure()
	{
		return (bool) $this->cookie_secure;
	}

	public function setCookieSecure($cookie_secure)
	{
		$this->cookie_secure = (bool) $cookie_secure;

		return $this;
	}

	public function getDefaultCookieExpire()
	{
		return (int) $this->cookie_expire;
	}

	public function setDefaultCookieExpire($cookie_expire)
	{
		$this->cookie_expire = (int) $cookie_expire;

		return $this;
	}

	public function getCookie($cookie_name)
	{
		if(!isset($this->cookies[(string) $cookie_name]))
		{
			return NULL;
		}

		return $this->cookies[(string) $cookie_name];
	}

	public function setCookie($cookie_name)
	{
		$cookie = \OpenFlame\Framework\Header\CookieInstance::newInstance($this);
		$cookie->setCookieName((string) $cookie_name);

		$this->cookies[(string) $cookie_name] = $cookie;

		return $cookie;
	}

	public function trashCookie($cookie_name)
	{
		unset($this->cookies[(string) $cookie_name]);

		return $this;
	}

	public function expireCookie($cookie_name)
	{
		if(!isset($this->cookies[(string) $cookie_name]))
		{
			$cookie = $this->setCookie($cookie_name);

		}
		else
		{
			$cookie = $this->cookies[(string) $cookie_name];
		}

		$cookie->setCookieValue('')
			->setExpireTime(0);

		return $this;
	}
}
