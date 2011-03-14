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
 * OpenFlame Web Framework - Asset mananger object
 * 	     Manages all asset instances and provides access plus some helpful debugging tools with them.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Manager
{
	protected $base_url = '';

	protected $assets = array();

	protected $exception_on_invalid_asset = false;

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
	 * @return \OpenFlame\Framework\Template\Asset\Manager - Provides a fluent interface.
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

	public function usingInvalidAssetExceptions()
	{
		return $this->exception_on_invalid_asset;
	}

	/**
	 * Set the asset manager to throw exceptions when an invalid asset is accessed, instead of returning NULL.
	 * @return \OpenFlame\Framework\Template\Asset\Manager - Provides a fluent interface.
	 */
	public function enableInvalidAssetExceptions()
	{
		$this->exception_on_invalid_asset = true;
		return $this;
	}

	/**
	 * Set the asset manager to return NULL when an invalid asset is accessed, instead of throwing exceptions.
	 * @return \OpenFlame\Framework\Template\Asset\Manager - Provides a fluent interface.
	 */
	public function disableInvalidAssetExceptions()
	{
		$this->exception_on_invalid_asset = false;
		return $this;
	}

	/**
	 *
	 * @return \OpenFlame\Framework\Template\Asset\AssetInstance - The newly created AssetInstance object.
	 */
	protected function registerAsset($asset_class = '\\OpenFlame\\Framework\\Template\\Asset\\AssetInstance')
	{
		// We want to make sure the class exists, even with autoloading -- if we can't load it, we need to asplode.
		if(!class_exists($asset_class, true))
		{
			throw new \InvalidArgumentException(sprintf('The class "%1$s" does not exist and cannot be instantiated in \\OpenFlame\\Framework\\Template\\Asset\\Manager->registerAsset()'));
		}

		// Require the use of the AssetInstanceInterface for the provided class
		if(!($asset_class instanceof \OpenFlame\Framework\Template\Asset\AssetInstanceInterface))
		{
			throw new \LogicException(sprintf('The class "%1$s" does not implement the interface \\OpenFlame\\Framework\\Template\\Asset\\AssetInstanceInterface as required', $asset_class));
		}

		return $asset_class::newInstance()->setBaseURL($this->getBaseURL());
	}

	protected function storeAsset(\OpenFlame\Framework\Template\Asset\AssetInstance $asset)
	{
		$this->assets[$asset->getType()][$asset->getName()] = $asset;

		return $asset;
	}

	public function registerCustomAsset($type, $name)
	{
		return $this->storeAsset($this->registerAsset()->setType($type)->setName($name));
	}

	public function registerJSAsset($name)
	{
		return $this->registerCustomAsset('js', $name);
	}

	public function registerCSSAsset($name)
	{
		return $this->registerCustomAsset('css', $name);
	}

	public function registerImageAsset($name)
	{
		return $this->registerCustomAsset('image', $name);
	}

	public function getAssetTypes()
	{
		return array_keys($this->assets);
	}

	public function getAssetsForType($type)
	{
		if(empty($this->assets[$type]))
		{
			return array();
		}

		return array_keys($this->assets[$type]);
	}

	public function getAsset($type, $name)
	{
		if(!isset($this->assets[$type]) || !isset($this->assets[$type][$name]))
		{
			if($this->usingInvalidAssetExceptions())
			{
				throw new \RuntimeException(sprintf('Attempted to access invalid asset "%1$s.%2$s"', $type, $name));
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
