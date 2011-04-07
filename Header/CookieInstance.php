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
	/**
	 * @var string - The name for this cookie instance
	 */
	protected $cookie_name = '';

	/**
	 * @var string - The value for this cookie instance
	 */
	protected $cookie_value = '';

	/**
	 * @var int - The time in seconds until this cookie should expire
	 */
	protected $expire_time = -1;

	/**
	 * @var \OpenFlame\Framework\Header\Submodule\Cookie - The cookie manager submodule
	 */
	protected $cookie_manager;

	/**
	 * Constructor
	 * @param \OpenFlame\Framework\Header\Submodule\Cookie $cookie_manager - The cookie manager submodule.
	 * @return void
	 */
	final protected function __construct(\OpenFlame\Framework\Header\Submodule\Cookie $cookie_manager)
	{
		$this->cookie_manager = $cookie_manager;
	}

	/**
	 * Get a new instance of the cookie instance object
	 * @param \OpenFlame\Framework\Header\Submodule\Cookie $cookie_manager - The cookie manager submodule.
	 * @return \OpenFlame\Framework\Header\CookieInstance - The newly created cookie instance
	 */
	final public static function newInstance(\OpenFlame\Framework\Header\Submodule\Cookie $cookie_manager)
	{
		return new static($cookie_manager);
	}

	/**
	 * Get the UNIX time that this cookie instance will expire
	 * @return integer - The UNIX timestamp of when this cookie will expire
	 */
	public function getExpireTime()
	{
		if($this->expire_time < 0)
		{
			$expire_time = $this->cookie_manager->getNowTime() + $this->cookie_manager->getDefaultCookieExpire();
		}
		else
		{
			$expire_time = $this->cookie_manager->getNowTime() + $this->expire_time;
		}

		return $expire_time;
	}

	/**
	 * Get the RFC-compliant time that this cookie instance will expire
	 * @return string - The RFC-compliant timestamp of when this cookie will expire
	 */
	public function getRFCExpireTime()
	{
		if($this->expire_time < 0)
		{
			$expire_time = $this->cookie_manager->getNowTime() + $this->cookie_manager->getDefaultCookieExpire();
		}
		else
		{
			$expire_time = $this->cookie_manager->getNowTime() + $this->expire_time;
		}

		return gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expire_time);
	}

	/**
	 * Set how many seconds into the future this cookie will expire.
	 * @param integer $expire_time - The time, in seconds, of how far into the future this cookie will expire.
	 * @return \OpenFlame\Framework\Header\CookieInstance - Provides a fluent interface.
	 */
	public function setExpireTime($expire_time = -1)
	{
		$this->expire_time = (int) $expire_time;

		return $this;
	}

	/**
	 * Get the name for this cookie instance.
	 * @return string - The name for the cookie instance.
	 */
	public function getCookieName()
	{
		return $this->cookie_name;
	}

	/**
	 * Set the cookie name for this instance.
	 * @param string $cookie_name - The name to set for this cookie instance.
	 * @return \OpenFlame\Framework\Header\CookieInstance - Provides a fluent interface.
	 */
	public function setCookieName($cookie_name)
	{
		/**
		 * This is to work around an oddity with PHP and cookies...
		 * It may not apply to us since we set cookies via header() instead of setcookie(), but better safe than sorry.
		 * @see: http://www.php.net/manual/en/function.setcookie.php#99845
		 */
		$cookie_name = str_replace('.', '_', (string) $cookie_name);
		$this->cookie_name = $cookie_name;

		return $this;
	}

	/**
	 * Get the value attached to this cookie instance
	 * @return string - The value attached to this cookie instance.
	 */
	public function getCookieValue()
	{
		return $this->cookie_value;
	}

	/**
	 * Attach a value to this cookie instance.
	 * @param string $cookie_value - The value to attach to this cookie instance.
	 * @return \OpenFlame\Framework\Header\CookieInstance - Provides a fluent interface.
	 */
	public function setCookieValue($cookie_value)
	{
		$this->cookie_value = (string) $cookie_value;

		return $this;
	}

	/**
	 * Get the full header string of this cookie's data.
	 * @return string - The header string to send for this cookie.
	 *
	 * @throws \LogicException
	 */
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
