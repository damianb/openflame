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
 * OpenFlame Web Framework - Template proxy object
 * 	     Provides smooth access to asset object instances in twig templates.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Proxy
{
	/**
	 * @var \OpenFlame\Framework\Template\Asset\Manager - The asset manager which handles all asset instances.
	 */
	protected $manager;

	/**
	 * @var array - Array of the subproxy instances.
	 */
	protected $subproxies = array();

	/**
	 * Constructor
	 * @param \OpenFlame\Framework\Template\Asset\Manager $manager - The template asset manager.
	 * @return void
	 */
	public function __construct(\OpenFlame\Framework\Template\Asset\Manager $manager)
	{
		$this->manager = $manager;
		foreach($this->manager->getAssetTypes() as $type)
		{
			$subproxy = \OpenFlame\Framework\Template\Asset\Subproxy::newInstance($manager)
				->setType($type)
				->populateAssetList();
			$this->subproxies[$type] = $subproxy;
		}
	}

	/**
	 * Magic method, providing seamless access to asset data in Twig templates.
	 * @param string $name - The type of the asset to grab.
	 * @return \OpenFlame\Framework\Template\Asset\Subproxy - The subproxy for the asset type that we want.
	 *
	 * @throws \RuntimeException
	 */
	public function __get($name)
	{
		if(!isset($this->subproxies[$name]))
		{
			if($this->manager->usingInvalidAssetExceptions())
			{
				throw new \RuntimeException(sprintf('Attempted to access invalid asset type "%1$s"', $name));
			}
			else
			{
				return NULL;
			}
		}

		return $this->subproxies[$name];
	}

	/**
	 * Magic method, providing seamless access to asset data in Twig templates.
	 * @param string $name - The type of asset to check for existence.
	 * @return boolean - Whether or not the subproxy exists.
	 */
	public function __isset($name)
	{
		return isset($this->subproxies[$name]);
	}

	/**
	 * @ignore
	 */
	public function __toString()
	{
		throw new \LogicException('The asset proxy cannot provide a direct value and is only for providing access to subproxies and asset instances');
	}
}
