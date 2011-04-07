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

namespace OpenFlame\Framework\Header;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Cookie instance object
 * 	     Represents the individual cookies that are to be set.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class CookieInstance
{
	protected $cookie_name = '';

	protected $cookie_value = '';

	protected $expire_time = -1;

	/**
	 * @var \OpenFlame\Framework\Header\Submodule\Cookie - The cookie manager submodule
	 */
	protected $cookie_manager;

	final protected function __construct(\OpenFlame\Framework\Header\Submodule\Cookie $cookie_manager)
	{
		$this->cookie_manager = $cookie_manager;
	}

	final public static function newInstance(\OpenFlame\Framework\Header\Submodule\Cookie $cookie_manager)
	{
		return new static($cookie_manager);
	}

	public function getExpireTime()
	{
		if($this->expire_time < 0)
		{
			$expire_time = $this->cookie_manager->getNowTime() + $this->cookie_manager->getDefaultCookieExpire();
		}
		else
		{
			$expire_time = $this->expire_time;
		}

		return $expire_time;
	}

	public function getRFCExpireTime()
	{
		if($this->expire_time < 0)
		{
			$expire_time = $this->cookie_manager->getNowTime() + $this->cookie_manager->getDefaultCookieExpire();
		}
		else
		{
			$expire_time = $this->expire_time;
		}

		return gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expire_time);
	}

	public function setExpireTime($expire_time = -1)
	{
		$this->expire_time = $expire_time;

		return $this;
	}

	public function getCookieName()
	{
		return $this->cookie_name;
	}

	public function setCookieName($cookie_name)
	{
		/**
		 * This is to work around an oddity with PHP and cookies...
		 * It may not apply to us since we set cookies via header() instead of setcookie(), but better safe than sorry.
		 * @see: http://www.php.net/manual/en/function.setcookie.php#99845
		 */
		$cookie_name = str_replace('.', '_', $cookie_name);
		$this->cookie_name = $cookie_name;

		return $this;
	}

	public function getCookieValue()
	{
		return $this->cookie_value;
	}

	public function setCookieValue($cookie_value)
	{
		$this->cookie_value = $cookie_value;

		return $this;
	}

	public function getFullCookieString()
	{
		if(empty($this->cookie_name))
		{
			throw new \LogicException('Attempted to generate cookie data string for a cookie without a cookie name set');
		}

		$path = $this->cookie_manager->getCookiePath();
		$domain = $this->cookie_manager->getCookieDomain();
		$use_secure = $this->cookie_manager->getCookieSecure();

		$cookie_data = array();
		$cookie_data[] = rawurlencode($this->cookie_manager->getCookiePrefix() . '_' . $this->cookie_name) . '=' . rawurlencode($this->cookie_value);
		if($this->expire_time !== 0)
		{
			$cookie_data[] = 'expires=' . $this->getRFCExpireTime();
		}
		if($path !== NULL)
		{
			$cookie_data[] = 'path=' . $path;
		}
		if($domain !== NULL)
		{
			$cookie_data[] = 'domain=' . $domain;
		}
		if($use_secure != false)
		{
			$cookie_data[] = 'secure';
		}
		$cookie_data[] = 'HttpOnly';

		$cookie_string = implode('; ', $cookie_data);
		return $cookie_string;
	}
}
