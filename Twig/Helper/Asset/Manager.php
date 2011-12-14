<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  asset
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Twig\Helper\Asset;
use OpenFlame\Framework\Twig\Helper\Asset\Internal\AssetManagerException;

/**
 * OpenFlame Framework - Asset mananger object
 * 	     Manages all asset instances and provides access plus some helpful debugging tools with them.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Manager
{
	/**
	 * @var string - The base url to prepend to all asset URLs.
	 */
	protected $base_url = '';

	/**
	 * @var string - The external domain to use for all asset URLs (assuming that external domains are being used instead of base URLs).
	 */
	protected $external_domain = '';

	/**
	 * @var boolean - Should we use an external domain for the base URL?
	 */
	protected $use_external_base = false;

	/**
	 * @var array - The array of asset instances.
	 */
	protected $assets = array();

	/**
	 * @var boolean - Do we want to throw an exception when accessing an invalid asset or asset type?
	 */
	protected $exception_on_invalid_asset = false;

	/**
	 * Get the base to prepend to all asset URLs
	 * @return string - The base to prepend.
	 */
	public function getBase()
	{
		if($this->use_external_base)
		{
			return $this->external_domain;
		}
		else
		{
			return $this->base_url;
		}
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
	 * @return \OpenFlame\Framework\Twig\Helper\Asset\Manager - Provides a fluent interface.
	 */
	public function setBaseURL($base_url)
	{
		$this->base_url = rtrim($base_url, '/'); // We don't want a trailing slash here.

		// We need to update the asset instances too.
		foreach($this->assets as $type)
		{
			foreach($type as $asset)
			{
				$asset->setBaseURL($base_url);
			}
		}

		return $this;
	}

	/**
	 * Check to see if we are using the external domain or local base URL for the base of our asset URLs.
	 * @return boolean - Are we using the external domain or the local base URL for asset URLs?
	 */
	public function usingExternalBase()
	{
		return (bool) $this->use_external_base;
	}

	/**
	 * Use the external domain instead of the local base URL for the base of all asset URLs
	 * @return \OpenFlame\Framework\Twig\Helper\Asset\Manager - Provides a fluent interface.
	 *
	 * @throws AssetManagerException
	 */
	public function enableExternalBase()
	{
		// If the external domain hasn't been set, we have a problem.
		if(empty($this->external_domain))
		{
			throw new AssetManagerException('Cannot use an empty external domain as base URL');
		}

		$this->use_external_base = true;

		return $this;
	}

	/**
	 * Use the local base URL instead of the external domain for the base of all asset URLs
	 * @return \OpenFlame\Framework\Twig\Helper\Asset\Manager - Provides a fluent interface.
	 */
	public function disableExternalBase()
	{
		$this->use_external_base = false;

		return $this;
	}

	/**
	 * Get the external domain to use for the base URL.
	 * @return string - The external domain to use for the base URL.
	 */
	public function getExternalBase()
	{
		return $this->external_domain;
	}

	/**
	 * Set the external domain to use for the base URL.
	 * @param string $base_url - The external domain to use.
	 * @return \OpenFlame\Framework\Twig\Helper\Asset\Manager - Provides a fluent interface.
	 *
	 * @throws AssetManagerException
	 */
	public function setExternalBase($base_url)
	{
		if(!filter_var($base_url, FILTER_VALIDATE_URL))
		{
			throw new AssetManagerException('Invalid external domain specified for asset URL base');
		}

		$this->external_domain = $base_url;

		return $this;
	}

	/**
	 * Do we want to use invalid asset exceptions, or return NULL?
	 * @return boolean - Whether or not to use invalid asset exceptions.
	 */
	public function usingInvalidAssetExceptions()
	{
		return $this->exception_on_invalid_asset;
	}

	/**
	 * Set the asset manager to throw exceptions when an invalid asset is accessed, instead of returning NULL.
	 * @return \OpenFlame\Framework\Twig\Helper\Asset\Manager - Provides a fluent interface.
	 */
	public function enableInvalidAssetExceptions()
	{
		$this->exception_on_invalid_asset = true;
		return $this;
	}

	/**
	 * Set the asset manager to return NULL when an invalid asset is accessed, instead of throwing exceptions.
	 * @return \OpenFlame\Framework\Twig\Helper\Asset\Manager - Provides a fluent interface.
	 */
	public function disableInvalidAssetExceptions()
	{
		$this->exception_on_invalid_asset = false;
		return $this;
	}

	/**
	 * Create a new asset instance.
	 * @param string $asset_class - The class to instantiate for the AssetInstance object
	 * @return AssetInstance - The newly created AssetInstance object.
	 */
	public function registerAsset()
	{
		$asset = AssetInstance::newInstance();
		$asset->setManager($this);

		return $asset;
	}

	/**
	 * Store the provided asset inside the manager.
	 * @param AssetInstance $asset - The asset instance to store.
	 * @return AssetInstance - The asset just stored.
	 */
	protected function storeAsset(AssetInstance $asset)
	{
		$this->assets[$asset->getType()][$asset->getName()] = $asset;

		return $asset;
	}

	/**
	 * Create a custom-type asset instance and store it in the manager.
	 * @param string $type - The asset type (java, flash, etc.).
	 * @param string $name - A unique name to refer to the asset.
	 * @return AssetInstance - The newly created asset instance.
	 */
	public function registerCustomAsset($type, $name)
	{
		return $this->storeAsset($this->registerAsset()->setType($type)->setName($name));
	}

	/**
	 * Create a new javascript asset instance and store it in the manager.
	 * @param string $name - A unique name to refer to the asset.
	 * @return AssetInstance - The newly created asset instance.
	 */
	public function registerJSAsset($name)
	{
		return $this->storeAsset($this->registerAsset()->setType('js')->setName($name));
	}

	/**
	 * Create a new cascading-stylesheet asset instance and store it in the manager.
	 * @param string $name - A unique name to refer to the asset.
	 * @return AssetInstance - The newly created asset instance.
	 */
	public function registerCSSAsset($name)
	{
		return $this->storeAsset($this->registerAsset()->setType('css')->setName($name));
	}

	/**
	 * Create a new XML asset instance and store it in the manager.
	 * @param string $name - A unique name to refer to the asset.
	 * @return AssetInstance - The newly created asset instance.
	 */
	public function registerXMLAsset($name)
	{
		return $this->storeAsset($this->registerAsset()->setType('xml')->setName($name));
	}

	/**
	 * Create a new image asset instance and store it in the manager.
	 * @param string $name - A unique name to refer to the asset.
	 * @return AssetInstance - The newly created asset instance.
	 */
	public function registerImageAsset($name)
	{
		return $this->storeAsset($this->registerAsset()->setType('image')->setName($name));
	}

	/**
	 * Get the array of currently defined asset types available
	 * @return array - The array of asset types currently defined.
	 */
	public function getAssetTypes()
	{
		return array_keys($this->assets);
	}

	/**
	 * Get an array of asset instance names of a specific asset type
	 * @param string $type - The asset type to grab the array of instance names of.
	 * @return array - The array of asset names for instances declared as the specified asset type.
	 */
	public function getAssetsForType($type)
	{
		if(empty($this->assets[$type]))
		{
			return array();
		}

		return array_keys($this->assets[$type]);
	}

	/**
	 * Get a stored asset instance object
	 * @param string $type - The asset type.
	 * @param string $name - The asset name.
	 * @return AssetInstance - The asset instance to grab.
	 *
	 * @throws AssetManagerException
	 */
	public function getAsset($type, $name)
	{
		if(!isset($this->assets[$type]) || !isset($this->assets[$type][$name]))
		{
			if($this->usingInvalidAssetExceptions())
			{
				throw new AssetManagerException(sprintf('Attempted to access invalid asset "%1$s.%2$s"', $type, $name));
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return $this->assets[$type][$name];
		}
	}
}
