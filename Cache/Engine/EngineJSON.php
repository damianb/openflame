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
use OpenFlame\Framework\Utility\JSON;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - JSON Cache engine,
 * 		JSON cache engine for use with the cache interface.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class EngineJSON extends EngineFileBase implements EngineInterface
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
		return 'JSON';
	}

	/**
	 * Builds a JSON-based cache file, complete with idiot warning.
	 * @param mixed $data - The data to cache.
	 * @return string - Full JSON code to be stored in a cache file.
	 */
	public function build($data)
	{
		$data = JSON::encode($data);

		return implode("\n", array(
			'# OpenFlame Web Framework cache file - modify at your own risk!',
			'# data ' . self::CHECKSUM_ALGO . ' checksum: { ' . hash(self::CHECKSUM_ALGO, $data) . ' }',
			'# engine: ' . $this->getEngineName(),
			$data,
		));
	}

	/**
	 * Loads a JSON cache file and returns the cached data.
	 * @param string $file - The file to load from.
	 * @return mixed - The cached data.
	 */
	public function load($file)
	{
		return JSON::decode($this->readFile("$file.json.tmp"));
	}

	/**
	 * Check to see if a cache file exists.
	 * @param string $file - The file to check.
	 * @return boolean - Has the data been cached?
	 */
	public function exists($file)
	{
		return $this->fileExists("$file.json.tmp");
	}

	/**
	 * Destroys a cache file.
	 * @param string $file - The cache file to destroy.
	 * @return void
	 */
	public function destroy($file)
	{
		$this->deleteFile("$file.json.tmp");
	}

	/**
	 * Stores data to a cache file.
	 * @param string $file - The cache file to store our data in.
	 * @param string $data - The data to cache.
	 * @return void
	 */
	public function store($file, $data)
	{
		$this->writeFile("$file.json.tmp", $data);
	}
}
