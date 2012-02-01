<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  asset
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Twig\Helper\Asset;

/**
 * OpenFlame Framework - Asset instance object
 * 	     Represents the individual asset instance and its properties.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class AssetInstance
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
	 * @var \emberlabs\openflame\Twig\Helper\Asset\Manager - The asset manager object.
	 */
	protected $manager;

	/**
	 * @var array - Array of various properties belonging to this asset instance.
	 */
	protected $properties = array();

	/**
	 * Get a new instance of this object.
	 * @return \emberlabs\openflame\Twig\Helper\Asset\AssetInstance - The newly created instance.
	 */
	public static function newInstance()
	{
		return new static();
	}

	/**
	 * Link this object and the asset manager
	 * @return \emberlabs\openflame\Twig\Helper\Asset\AssetInstance - Provides a fluent interface.
	 */
	public function setManager(Manager $manager)
	{
		$this->manager = $manager;

		return $this;
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
	 * @return \emberlabs\openflame\Twig\Helper\Asset\AssetInstance - Provides a fluent interface.
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
	 * @return \emberlabs\openflame\Twig\Helper\Asset\AssetInstance - Provides a fluent interface.
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
	 * @return \emberlabs\openflame\Twig\Helper\Asset\AssetInstance - Provides a fluent interface.
	 */
	public function setURL($url)
	{
		$this->url = '/' . ltrim($url, '/');

		return $this;
	}

	/**
	 * Get a property of this asset instance.
	 * @param string $property - The name of the property to grab.
	 * @return mixed - NULL if no such property set, or the value of the property.
	 */
	public function getProperty($property)
	{
		if(isset($this->properties[(string) $property]))
		{
			return NULL;
		}

		return $this->properties[(string) $property];
	}

	/**
	 * Set an asset's property.
	 * @param string $property - The name of the property to set.
	 * @param mixed $value - The value to set for the property.
	 * @return \emberlabs\openflame\Twig\Helper\Asset\AssetInstance - Provides a fluent interface.
	 */
	public function setProperty($property, $value)
	{
		$this->properties[(string) $property] = $value;

		return $this;
	}

	/**
	 * Check to see if a specific property exists.
	 * @param string $name - The name of the property to check.
	 * @return bool - Has the property been set?
	 */
	public function __isset($name)
	{
		return isset($this->properties[(string) $name]);
	}

	/**
	 * Get a property of this asset instance.
	 * @param string $property - The name of the property to grab.
	 * @return mixed - NULL if no such property set, or the value of the property.
	 */
	public function __get($name)
	{
		if(isset($this->properties[(string) $property]))
		{
			return NULL;
		}

		return $this->properties[(string) $property];
	}

	/**
	 * Get the full URL for this specific asset
	 * @return string - The full (absolute) URL to the asset.
	 */
	public function __toString()
	{
		return rtrim($this->manager->getBase(), '/') . $this->url;
	}
}
