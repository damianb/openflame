<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  cache
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Cache\Engine\File;

/**
 * OpenFlame Framework - serialize() Cache engine,
 * 		serialize() cache engine for use with the cache interface.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class FileEngineSerialize extends FileEngineBase implements \OpenFlame\Framework\Cache\Engine\EngineInterface
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
	 * Get the extension for cache files made/used by this engine (e.g. cache_key.{$ext}.tmp)
	 * @return string - The cache file extension.
	 */
	protected function getFileExtension()
	{
		return 'srl';
	}

	/**
	 * Builds a serialize()-based cache file, complete with idiot warning.
	 * @param mixed $data - The data to cache.
	 * @return string - Full JSON code to be stored in a cache file.
	 */
	protected function engineBuild($data)
	{
		$data = serialize($data);

		return implode("\n", array(
			'# OpenFlame Framework cache file - modify at your own risk!',
			'# data ' . self::CHECKSUM_ALGO . ' checksum: { ' . hash(self::CHECKSUM_ALGO, $data) . ' }',
			'# engine: ' . $this->getEngineName(),
			$data,
		));
	}

	/**
	 * Loads a serialize() cache file and returns the cached data.
	 * @param string $key - The file to load from.
	 * @return mixed - The cached data.
	 */
	protected function engineLoad($key)
	{
		$data = $this->readFile("$key.srl.tmp");
		$data = preg_replace("/#.*?\n/", '', $data);
		return unserialize($data);
	}

	/**
	 * Check to see if a cache file exists.
	 * @param string $file - The file to check.
	 * @return boolean - Has the data been cached?
	 */
	public function exists($key)
	{
		return $this->fileExists("$key.srl.tmp");
	}

	/**
	 * Destroys a cache file.
	 * @param string $key - The cache file to destroy.
	 * @return void
	 */
	public function destroy($key)
	{
		$this->deleteFile("$key.srl.tmp");
	}

	/**
	 * Stores data to a cache file.
	 * @param string $key - The cache file to store our data in.
	 * @param string $data - The data to cache.
	 * @param integer $ttl - The lifespan of the cached data, in seconds.  Leave empty or set as 0 to disable cache timeout.
	 * @return void
	 */
	public function store($key, $data)
	{
		$this->writeFile("$key.srl.tmp", $this->build($data, $ttl));
	}
}
