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
use \emberlabs\openflame\Core\Internal\RuntimeException;

/**
 * OpenFlame Framework - Template subproxy object
 * 	     Provides smooth access to asset object instances in twig templates
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class Subproxy
{
	/**
	 * @var \emberlabs\openflame\Twig\Helper\Asset\Manager - The asset manager which handles all asset instances.
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
	 * @param \emberlabs\openflame\Twig\Helper\Asset\Manager $manager - The template asset manager.
	 * @return void
	 */
	public function __construct(Manager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Grab a new instance of the subproxy
	 * @param \emberlabs\openflame\Twig\Helper\Asset\Manager $manager - The template asset manager.
	 * @return \emberlabs\openflame\Twig\Helper\Asset\Subproxy - The newly created instance.
	 */
	public static function newInstance(Manager $manager)
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
	 * @param string $type - The asset type to set.
	 * @return \emberlabs\openflame\Twig\Helper\Asset\Subproxy - Provides a fluent interface.
	 */
	public function setType($type)
	{
		$this->type = (string) $type;
		return $this;
	}

	/**
	 * Populate the internal cache list of asset instances that this subproxy can access.
	 * @return \emberlabs\openflame\Twig\Helper\Asset\Subproxy - Provides a fluent interface.
	 */
	public function populateAssetList()
	{
		$this->asset_list = $this->manager->getAssetsForType($this->getType());
		return $this;
	}

	/**
	 * Magic method, providing seamless access to asset data in Twig templates.
	 * @param string $name - The name of the asset to grab.
	 * @return \emberlabs\openflame\Twig\Helper\Asset\AssetInstance - The asset instance for the asset that we want.
	 *
	 * @throws RuntimeException
	 */
	public function __get($name)
	{
		if(!in_array($name, $this->asset_list))
		{
			if($this->manager->usingInvalidAssetExceptions())
			{
				throw new RuntimeException(sprintf('Attempted to access invalid asset "%1$s.%2$s"', $this->getType(), $name));
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
		return in_array($name, $this->asset_list);
	}

	/**
	 * @ignore
	 */
	public function __toString()
	{
		return '';
	}
}
