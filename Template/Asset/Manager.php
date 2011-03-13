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

	protected $assets = '';

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

		return $this;
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
}
