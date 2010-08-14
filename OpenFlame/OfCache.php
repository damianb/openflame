<?php
/**
 *
 * @package OpenFlame Web Framework
 * @copyright (c) 2010 OpenFlameCMS.com
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - Cache interface class,
 * 		Provides an easy-to-use object interface for interacting with the loaded cache engine.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
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
		// include './Cache/Engine' . $engine . '.php';
		$this->engine = new $cache_engine($this->cache_path);
		if(!($this->engine instanceof OfCacheEngineBase))
			throw new OfCacheException('Cache engine does not extend OfCacheEngineBase class', OfCacheException::ERR_CACHE_ENGINE_NOT_CACHEBASE_CHILD);
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
		if(!$this->dataCached($file))
			return NULL;
		return $this->engine->load($file);
	}

	/**
	 * Public interface, stores data in cache.
	 * @param string $file - The file to store the data in.
	 * @param mixed $data - The data to store.
	 * @return void
	 */
	public function storeData($file, $data)
	{
		$this->engine->store($file, $this->engine->build($data));
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
 */
class OfCacheEngineBase
{
	// asdf
}

/**
 * OpenFlame Web Framework - Cache Engine interface,
 * 		Cache engine prototype, declares required methods that a cache engine must define in order to be valid.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 */
interface OfCacheEngineInterface
{
	public function build($data);
	public function load($file);
	public function exists($file);
	public function destroy($file);
	public function store($file, $data);
}
