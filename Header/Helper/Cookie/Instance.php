<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  cookie
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Cookie;
use OpenFlame\Framework\Header\Helper\Cookie\Internal\CookieInstanceException;

/**
 * OpenFlame Framework - Cookie instance object
 * 	     Represents the individual cookie that is to be set.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Instance
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
	 * @var \OpenFlame\Framework\Cookie\Manager - The cookie manager submodule
	 */
	protected $manager;

	const RFC_COOKIE_DATE_FORMAT = 'D, d-M-Y H:i:s \\G\\M\\T';

	/**
	 * Constructor
	 * @param \OpenFlame\Framework\Header\Helper\Cookie\Manager $manager - The cookie manager submodule.
	 * @return void
	 */
	final protected function __construct(Manager $manager)
	{
		$this->manager = $manager;
		$this->expire_time = $this->manager->getDefaultCookieExpire();
	}

	/**
	 * Get a new instance of the cookie instance object
	 * @param \OpenFlame\Framework\Header\Helper\Cookie\Manager $manager - The cookie manager submodule.
	 * @return \OpenFlame\Framework\Header\Helper\Cookie\Instance - The newly created cookie instance
	 */
	final public static function newInstance(Manager $manager)
	{
		return new static($manager);
	}

	/**
	 * Get the UNIX time that this cookie instance will expire
	 * @return integer - The UNIX timestamp of when this cookie will expire
	 */
	public function getExpireTime()
	{
		return $this->manager->getNowTime() + $this->expire_time;
	}

	/**
	 * Get the RFC-compliant time that this cookie instance will expire
	 * @return string - The RFC-compliant timestamp of when this cookie will expire
	 */
	public function getRFCExpireTime()
	{
		return gmdate(self::RFC_COOKIE_DATE_FORMAT, $this->getExpireTime());
	}

	/**
	 * Set how many seconds into the future this cookie will expire.
	 * @param integer $expire_time - The time, in seconds, of how far into the future this cookie will expire.
	 * @return \OpenFlame\Framework\Header\Helper\Cookie\Instance - Provides a fluent interface.
	 */
	public function setExpireTime($expire_time = 0)
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
	 * @return \OpenFlame\Framework\Header\Helper\Cookie\Instance - Provides a fluent interface.
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
	 * @return \OpenFlame\Framework\Header\Helper\Cookie\Instance - Provides a fluent interface.
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
	 * @throws CookieInstanceException
	 */
	public function getFullCookieString()
	{
		if(empty($this->cookie_name))
		{
			throw new CookieInstanceException('Attempted to generate cookie data string for a cookie without a cookie name set');
		}

		$path = $this->manager->getCookiePath();
		$domain = $this->manager->getCookieDomain();
		$use_secure = $this->manager->usingSecureCookies();

		$cookie_data = array();
		$cookie_data[] = rawurlencode($this->manager->getCookiePrefix() . $this->cookie_name) . '=' . rawurlencode($this->cookie_value);
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
