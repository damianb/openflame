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
 * OpenFlame Web Framework - Template subproxy object
 * 	     Provides smooth access to asset object instances in twig templates
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Subproxy
{
	/**
	 * @var \OpenFlame\Framework\Template\Asset\Manager - The asset manager which handles all asset instances.
	 */
	protected $manager;

	/**
	 * @var string - The type of asset this subproxy handles.
	 */
	protected $type = '';

	/**
	 * @var array - Array of the assets that this subproxy handles.
	 */
	protected $asset_list = array();

	/**
	 * Constructor
	 * @param \OpenFlame\Framework\Template\Asset\Manager $manager - The template asset manager.
	 * @return void
	 */
	public function __construct(\OpenFlame\Framework\Template\Asset\Manager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Grab a new instance of the subproxy
	 * @param \OpenFlame\Framework\Template\Asset\Manager $manager - The template asset manager.
	 * @return \OpenFlame\Framwork\Template\Asset\Subproxy - The newly created instance.
	 */
	public static function newInstance(\OpenFlame\Framework\Template\Asset\Manager $manager)
	{
		return new static($manager);
	}

	/**
	 * Get the asset type of this subproxy
	 * @return string - The asset type this subproxy object represents.
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * Set the asset type for this subproxy
	 * @param string - The asset type to set.
	 * @return \OpenFlame\Framwork\Template\Asset\Subproxy - Provides a fluent interface.
	 */
	public function setType($type)
	{
		$this->type = (string) $type;
		return $this;
	}

	/**
	 * Populate the internal cache list of asset instances that this subproxy can access.
	 * @return \OpenFlame\Framwork\Template\Asset\Subproxy - Provides a fluent interface.
	 */
	public function populateAssetList()
	{
		$this->asset_list = $this->manager->getAssetsForType($this->getType());
		return $this;
	}

	/**
	 * Magic method, providing seamless access to asset data in Twig templates.
	 * @param string $name - The name of the asset to grab.
	 * @return \OpenFlame\Framework\Template\Asset\AssetInstanceInterface - The asset instance for the asset that we want.
	 *
	 * @throws \RuntimeException
	 */
	public function __get($name)
	{
		if(!isset($this->asset_list[$name]))
		{
			if($this->manager->usingInvalidAssetExceptions())
			{
				throw new \RuntimeException(sprintf('Attempted to access invalid asset "%1$s.%2$s"', $this->getType(), $name));
			}
			else
			{
				return NULL;
			}
		}

		return $this->manager->getAsset($this->getType(), $name);
	}

	/**
	 * Magic method, providing seamless access to asset data in Twig templates.
	 * @param string $name - The name of the asset to check for existance.
	 * @return boolean - Whether or not the asset instance exists.
	 */
	public function __isset($name)
	{
		return isset($this->asset_list[$name]);
	}

	/**
	 * @ignore
	 */
	public function __toString()
	{
		throw new \LogicException('The asset subproxy cannot provide a direct value and is only for providing access to subproxies and asset instances');
	}
}
