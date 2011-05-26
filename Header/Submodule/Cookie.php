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
	 * @var \OpenFlame\Framework\Cookie\Manager - The cookie manager object.
	 */
	protected $cookie_manager;

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
	 * Link in the cookie manager object
	 * @param \OpenFlame\Framework\Cookie\Manager $manager - The cookie manager object.
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function setCookieManager(\OpenFlame\Framework\Cookie\Manager $cookie_manager)
	{
		$this->cookie_manager = $cookie_manager;

		return $this;
	}

	/**
	 * Inject the headers for this submodule into the manager
	 * @return \OpenFlame\Framework\Header\Submodule\Cookie - Provides a fluent interface.
	 */
	public function injectHeaders()
	{
		$cookies = $this->cookie_manager->getAllCookies();
		foreach($cookies as $cookie)
		{
			$this->manager->setHeader('Set-Cookie', $cookie->getFullCookieString());
		}

		return $this;
	}
}
