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

namespace OpenFlame\Framework\Cache\Engine;
use \OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - serialize() Cache engine,
 * 		serialize() cache engine for use with the cache interface.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class EngineSerialize extends \OpenFlame\Framework\Cache\Engine\EngineFileBase implements \OpenFlame\Framework\Cache\Engine\EngineInterface
{
	/**
	 * @const - The algorithm to use for checksum of the cache file's cache contents
	 */
	const CHECKSUM_ALGO = 'md5';

	/**
	 * Get the engine name.
	 * @return string - The engine name.
	 */
	public function getEngineName()
	{
		return 'serialize';
	}

	/**
	 * Builds a serialize()-based cache file, complete with idiot warning.
	 * @param mixed $data - The data to cache.
	 * @return string - Full JSON code to be stored in a cache file.
	 */
	public function build($data)
	{
		$data = serialize($data);

		return implode("\n", array(
			'# OpenFlame Web Framework cache file - modify at your own risk!',
			'# data ' . self::CHECKSUM_ALGO . ' checksum: { ' . hash(self::CHECKSUM_ALGO, $data) . ' }',
			'# engine: ' . $this->getEngineName(),
			$data,
		));
	}

	/**
	 * Loads a serialize() cache file and returns the cached data.
	 * @param string $file - The file to load from.
	 * @return mixed - The cached data.
	 */
	public function load($file)
	{
		$data = $this->readFile("$file.srl.tmp");
		$data = preg_replace("/#.*?\n/", '', $data);
		return unserialize($data);
	}

	/**
	 * Check to see if a cache file exists.
	 * @param string $file - The file to check.
	 * @return boolean - Has the data been cached?
	 */
	public function exists($file)
	{
		return $this->fileExists("$file.srl.tmp");
	}

	/**
	 * Destroys a cache file.
	 * @param string $file - The cache file to destroy.
	 * @return void
	 */
	public function destroy($file)
	{
		$this->deleteFile("$file.srl.tmp");
	}

	/**
	 * Stores data to a cache file.
	 * @param string $file - The cache file to store our data in.
	 * @param string $data - The data to cache.
	 * @return void
	 */
	public function store($file, $data)
	{
		$this->writeFile("$file.srl.tmp", $data);
	}
}
