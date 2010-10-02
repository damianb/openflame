<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Cache interface class,
 * 		Provides an easy-to-use object interface for interacting with the loaded cache engine.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfCache
{
	/**
	 * @var OfCacheEngineInterface - The cache engine we are using.
	 */
	protected $engine;

	/**
	 * @var string - The path to where cache files will be stored, if we are using a file-based cache engine.
	 */
	protected $cache_path = '';

	/**
	 * Constructor
	 * @param string $cache_path - The path to where cache files will be stored, if the engine is file-based.
	 * @param string $engine - The name of the caching engine to use.
	 * @return void
	 *
	 * @throws OfCacheException
	 */
	public function __construct($engine, $cache_path = false)
	{
		$cache_engine = "OfCacheEngine$engine";
		$this->cache_path = (string) $cache_path;
		if(!@file_exists($this->cache_path) || !@is_dir($this->cache_path) || !@is_writeable($this->cache_path))
			throw new OfCacheException('Specified cache path is not accessible', OfCacheException::ERR_CACHE_PATH_NO_ACCESS);
		$this->engine = new $cache_engine($this->cache_path);
		if(!($this->engine instanceof OfCacheEngineBase))
			throw new OfCacheException('Cache engine does not extend OfCacheEngineBase', OfCacheException::ERR_CACHE_ENGINE_NOT_CACHEBASE_CHILD);
		if(!($this->engine instanceof OfCacheEngineInterface))
			throw new OfCacheException('Cache engine does not implement interface OfCacheEngineInterface', OfCacheException::ERR_CACHE_ENGINE_NOT_CACHEINTERFACE_CHILD);
	}

	/**
	 * Public interface, loads cached data from a given file, so long as it exists.
	 * @param string $file - The file to load cached data from.
	 * @return mixed - The previously cached data.
	 */
	public function loadData($file)
	{
		// if data is not cached already, return null
		if(!$this->dataCached($file))
			return NULL;
		$cache = $this->engine->load($file);
		// check ttl.  If the data has expired, trash it and return null.
		if(isset($cache['cache_expire']) && $cache['cache_expire'] != 0 && time() > $cache['cache_expire'])
		{
			$this->destroyData($file);
			return NULL;
		}
		return $cache['data'];
	}

	/**
	 * Public interface, stores data in cache.
	 * @param string $file - The file to store the data in.
	 * @param mixed $data - The data to store.
	 * @param integer $ttl - The lifespan of the cached data, in seconds.  Leave empty or set as 0 to disable cache timeout.
	 * @return void
	 */
	public function storeData($file, $data, $ttl = 0)
	{
		// build the cache, with data and ttl expiry included
		$this->engine->store($file, $this->engine->build(array(
			'data' => $data,
			'cache_expire' => ($ttl) ? time() + (int) $ttl : 0,
		)));
	}

	/**
	 * Public interface, checks to see if data has been cached already or not.
	 * @param $file - The file to check.
	 * @return boolean - Has the data been cached?
	 */
	public function dataCached($file)
	{
		return $this->engine->exists($file);
	}

	/**
	 * Public interface, destroys the specified cache file.
	 * @param string $file - The cache file to destroy.
	 * @return void
	 */
	public function destroyData($file)
	{
		$this->engine->destroy($file);
	}
}

/**
 * OpenFlame Web Framework - Cache Engine base class,
 * 		Cache engine prototype, provides some common methods for all engines to use.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfCacheEngineBase
{
	/**
	 * @var string - The path to where cache files will be stored, if we are using a file-based cache engine.
	 */
	protected $cache_path = '';

	/**
	 * Constructor
	 * @param string $cache_path - The path to where cache files will be stored, if the engine is file-based.
	 * @return void
	 */
	public function __construct($cache_path)
	{
		$this->cache_path = (string) $cache_path;
	}

	/**
	 * Writes data to a specified cache file
	 * @param string $file - The file to write to.
	 * @param string $data - The data to write to the cache file.
	 * @return void
	 *
	 * @throws OfCacheException
	 */
	protected function writeFile($file, $data)
	{
		$file = $this->cache_path . '/' . basename($file);
		if(@file_exists($file) && !@is_writable($file))
			throw new OfCacheException("Cache file '$file' is unwritable", OfCacheException::ERR_CACHE_UNWRITABLE);
		if(!$f = @fopen($file, 'w'))
			throw new OfCacheException("fopen() call failed for cache file '$file'", OfCacheException::ERR_CACHE_FOPEN_FAILED);
		if(@flock($f, LOCK_EX))
		{
			$length = @fwrite($f, $data);
			if($length !== strlen($data))
				throw new OfCacheException("fwrite() call failed for cache file '$file'", OfCacheException::ERR_CACHE_FWRITE_FAILED);
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new OfCacheException("flock() call failed for cache file '$file'", OfCacheException::ERR_CACHE_FLOCK_FAILED);
		}
		@fclose($f);
	}

	/**
	 * Reads a specified cache file's contents.
	 * @param string $file - The file to read from.
	 * @return string - The file's data
	 *
	 * @throws OfCacheException
	 */
	protected function readFile($file)
	{
		$file = $this->cache_path . '/' . basename($file);
		if(!@is_readable($file))
			throw new OfCacheException("Cache file '$file' is unreadable", OfCacheException::ERR_CACHE_UNREADABLE);
		if(!$f = @fopen($file, 'r'))
			throw new OfCacheException("fopen() call failed for cache file '$file'", OfCacheException::ERR_CACHE_FOPEN_FAILED);
		if(@flock($f, LOCK_EX))
		{
			$data = @fread($f, @filesize($file));
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new OfCacheException("flock() call failed for cache file '$file'", OfCacheException::ERR_CACHE_FLOCK_FAILED);
		}
		@fclose($f);

		return $data;
	}

	/**
	 * Checks to see if a specified cache file exists.
	 * @param string $file - The name of the cache file.
	 * @return boolean - Does the file exist?
	 */
	protected function fileExists($file)
	{
		return file_exists($this->cache_path . '/' . basename($file));
	}

	/**
	 * Deletes a specified file in the cache.
	 * @param string $file - The file to delete.
	 * @return boolean - Was the deletion successful?
	 */
	protected function deleteFile($file)
	{
		return @unlink($this->cache_path . '/' . basename($file));
	}
}

/**
 * OpenFlame Web Framework - Cache Engine interface,
 * 		Cache engine prototype, declares required methods that a cache engine must define in order to be valid.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
interface OfCacheEngineInterface
{
	public function build($data);
	public function load($file);
	public function exists($file);
	public function destroy($file);
	public function store($file, $data);
}
