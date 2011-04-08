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
	/**
	 * @var \OpenFlame\Framework\Header\Manager - The header manager object.
	 */
	protected $manager;

	/**
	 * @var integer - The current UNIX timestamp.
	 */
	protected $now = 0;

	/**
	 * @var array - The array of cookie instance instantiated.
	 */
	protected $cookies = array();

	/**
	 * @var string - The cookie domain to use for all cookies.
	 */
	protected $cookie_domain = '';

	/**
	 * @var string - The cookie path to use for all cookies.
	 */
	protected $cookie_path = '';

	/**
	 * @var string - The prefix to use for all cookie names.
	 */
	protected $cookie_prefix = 'opflame';

	/**
	 * @var boolean - Should cookies be marked as "secure"?
	 */
	protected $cookie_secure = false;

	/**
	 * @var integer - The default cookie expire time for all cookies.
	 */
	protected $cookie_expire = 3600; // @todo find a sane default for cookie expire time o_O

	/**
	 * Constructor
	 */
	protected function __construct() { }

	/**
	 * Get a new instance of ourselves.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - The newly created cookie manager submodule instance.
	 */
	public static function newInstance()
	{
		$self = new static();
		$self->setNowTime();
		return $self;
	}

	/**
	 * Link this submodule to the header manager object
	 * @param \OpenFlame\Framework\Header\Manager $manager - The header manager object.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function setManager(\OpenFlame\Framework\Header\Manager $manager)
	{
		$this->manager = $manager;

		return $this;
	}

	/**
	 * Inject the headers for this submodule into the manager
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function injectHeaders()
	{
		foreach($this->cookies as $cookie)
		{
			$this->manager->setHeader('Set-Cookie', $cookie->getFullCookieString());
		}

		return $this;
	}

	/**
	 * Get the current UNIX timestamp.
	 * @return integer - The current UNIX timestamp.
	 */
	public function getNowTime()
	{
		return $this->now;
	}

	/**
	 * Set the current UNIX timestamp (calls time() and stores the output, does not accept input)
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	protected function setNowTime()
	{
		$this->now = time();

		return $this;
	}

	/**
	 * Get the current cookie domain.
	 * @return string - The current cookie domain.
	 */
	public function getCookieDomain()
	{
		return $this->cookie_domain;
	}

	/**
	 * Set the cookie domain.
	 * @param string $cookie_domain - The cookie domain to set.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function setCookieDomain($cookie_domain)
	{
		// @todo validation of some sort?
		$this->cookie_domain = (string) $cookie_domain;

		return $this;
	}

	/**
	 * Get the current cookie path.
	 * @return string - The current cookie path.
	 */
	public function getCookiePath()
	{
		return $this->cookie_path;
	}

	/**
	 * Set the cookie path.
	 * @param string $cookie_path - The cookie path to set.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function setCookiePath($cookie_path)
	{
		// @todo validation of some sort?
		$this->cookie_path = (string) $cookie_path;

		return $this;
	}

	/**
	 * Get the current cookie prefix.
	 * @return string - The current cookie prefix.
	 */
	public function getCookiePrefix()
	{
		return $this->cookie_prefix;
	}

	/**
	 * Set the cookie prefix.
	 * @param string $cookie_prefix - The cookie prefix to set.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function setCookiePrefix($cookie_prefix)
	{
		$this->cookie_prefix = (string) $cookie_prefix;

		return $this;
	}

	/**
	 * Get whether or not the cookie should be marked "secure"
	 * @return boolean - Should the cookie be marked secure?
	 */
	public function getCookieSecure()
	{
		return (bool) $this->cookie_secure;
	}

	/**
	 * Set the cookie "secure" status.
	 * @param boolean $cookie_secure - Should this cookie be marked secure?
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function setCookieSecure($cookie_secure)
	{
		$this->cookie_secure = (bool) $cookie_secure;

		return $this;
	}

	/**
	 * Get the current default cookie expire time.
	 * @return integer - The current default cookie expire time.
	 */
	public function getDefaultCookieExpire()
	{
		return (int) $this->cookie_expire;
	}

	/**
	 * Set the default cookie expire time.
	 * @param string $cookie_expire - The expire time to set.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function setDefaultCookieExpire($cookie_expire)
	{
		$this->cookie_expire = (int) $cookie_expire;

		return $this;
	}

	/**
	 * Get a previously-created cookie instance.
	 * @param string $cookie_name - The name of the cookie instance to grab.
	 * @return mixed - Returns the cookie instance if it exists, or NULL if it does not.
	 */
	public function getCookie($cookie_name)
	{
		if(!isset($this->cookies[(string) $cookie_name]))
		{
			return NULL;
		}

		return $this->cookies[(string) $cookie_name];
	}

	/**
	 * Create a new cookie instance
	 * @param string $cookie_name - The name for this cookie instance to use.
	 * @return \OpenFlame\Framework\Header\CookieInstance - The newly created cookie instance.
	 */
	public function setCookie($cookie_name)
	{
		$cookie = \OpenFlame\Framework\Header\CookieInstance::newInstance($this);
		$cookie->setCookieName((string) $cookie_name);

		$this->cookies[(string) $cookie_name] = $cookie;

		return $cookie;
	}

	/**
	 * Remove a cookie that was previously going to be sent.
	 * @param string $cookie_name - The name of the cookie to trash.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function trashCookie($cookie_name)
	{
		unset($this->cookies[(string) $cookie_name]);

		return $this;
	}

	/**
	 * Set a cookie as "expired".
	 * @param string $cookie_name - The name of the cookie to expire.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
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
