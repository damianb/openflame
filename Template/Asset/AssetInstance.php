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

namespace OpenFlame\Framework\Template\Asset;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Asset instance object
 * 	     Represents the individual asset instance and its properties.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class AssetInstance implements \OpenFlame\Framework\Template\Asset\AssetInstanceInterface
{
	protected $name = '';

	protected $type = '';

	protected $base_url = '';

	protected $url = '';

	public static function newInstance()
	{
		return new static();
	}

	public function getName()
	{
		return $this->name;
	}

	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = (string) $type;
		return $this;
	}

	public function getURL()
	{
		return $this->url;
	}

	public function setURL($url)
	{
		$this->url = '/' . ltrim($url, '/');
		return $this;
	}

	/**
	 * Get the "base URL" of this installation, which is added to the beginning of all asset URLs automatically.
	 * @return string - The base URL we are using.
	 */
	public function getBaseURL()
	{
		return $this->base_url;
	}

	/**
	 * Set the "base URL" for this installation, which will be added to the beginning of all asset URLs.
	 * @param string $base_url - The "base URL" which we're going to prepend.
	 * @return \OpenFlame\Framework\Template\Asset\AssetInstance - Provides a fluent interface.
	 */
	public function setBaseURL($base_url)
	{
		$this->base_url = rtrim($base_url, '/'); // We don't want a trailing slash here.

		return $this;
	}

	public function __toString()
	{
		return $this->url;
	}
}
