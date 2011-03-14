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
	/**
	 * @var string - The name for this instance.
	 */
	protected $name = '';

	/**
	 * @var string - The asset type for this instance.
	 */
	protected $type = '';

	/**
	 * @var string - The relative URL for this instance.
	 */
	protected $url = '';

	/**
	 * @var string - The base URL used across all asset instances.
	 */
	protected $base_url = '';

	/**
	 * Get a new instance of this object.
	 * @return \OpenFlame\Framework\Template\Asset\AssetInstance - The newly created instance.
	 */
	public static function newInstance()
	{
		return new static();
	}

	/**
	 * Get the asset name of this instance
	 * @return string - The asset name for this instance.
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Set the asset type for this instance
	 * @param string $name - The asset name to set.
	 * @return \OpenFlame\Framwork\Template\Asset\AssetInstance - Provides a fluent interface.
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;
	}

	/**
	 * Get the asset type of this instance
	 * @return string - The asset type for this instance.
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set the asset type for this instance
	 * @param string $type - The asset type to set.
	 * @return \OpenFlame\Framwork\Template\Asset\AssetInstance - Provides a fluent interface.
	 */
	public function setType($type)
	{
		$this->type = (string) $type;
		return $this;
	}

	/**
	 * Get the relative asset URL for this instance
	 * @return string - The relative asset URL for this instance.
	 */
	public function getURL()
	{
		return $this->url;
	}

	/**
	 * Set the relative asset URL for this instance
	 * @param string - The relative asset URL to set.
	 * @return \OpenFlame\Framwork\Template\Asset\AssetInstance - Provides a fluent interface.
	 */
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

	/**
	 * Get the full URL for this specific asset
	 * @return string - The full (absolute) URL to the asset.
	 */
	public function __toString()
	{
		return $this->base_url . $this->url;
	}
}
