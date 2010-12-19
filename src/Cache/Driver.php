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

namespace OpenFlame\Framework\Cache;

if(!defined('OpenFlame\\Framework\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Cache interface class,
 * 		Provides an easy-to-use object interface for interacting with the loaded cache engine.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Driver
{
	/**
	 * @var \OpenFlame\Framework\Cache\Engine\EngineInterface - The cache engine we are using.
	 */
	protected $engine;

	/**
	 * Constructor
	 * @param \OpenFlame\Framework\Cache\Engine\EngineInterface $engine - The caching engine to use.
	 * @return void
	 */
	public function __construct(\OpenFlame\Framework\Cache\Engine\EngineInterface $engine)
	{
		$this->setEngine($engine);
	}

	/**
	 * Dependency injection method, sets the cache engine to be used
	 * @param \OpenFlame\Framework\Cache\Engine\EngineInterface - The cache engine to use.
	 * @return \OpenFlame\Framework\Cache\Driver - Provides a fluent interface.
	 */
	public function setEngine(\OpenFlame\Framework\Cache\Engine\EngineInterface $engine)
	{
		$this->engine = $engine;
		return $this;
	}

	/**
	 * Get the cache engine currently in use.
	 * @return \OpenFlame\Framework\Cache\Engine\EngineInterface - The cache engine in use.
	 */
	public function getEngine()
	{
		return $this->engine;
	}

	/**
	 * Public interface, checks to see if data has been cached already or not.
	 * @param $index - The index to check.
	 * @return boolean - Has the data been cached?
	 */
	public function dataCached($index)
	{
		return $this->getEngine()->exists($index);
	}

	/**
	 * Public interface, loads cached data from a given index, so long as it exists.
	 * @param string $index - The index to load cached data from.
	 * @return mixed - The previously cached data.
	 */
	public function loadData($index)
	{
		// if data is not cached already, return null
		if(!$this->dataCached($index))
			return NULL;

		$cache = $this->getEngine()->load($index);

		// check ttl.  If the data has expired, trash it and return null.
		if(isset($cache['cache_expire']) && $cache['cache_expire'] != 0 && time() > $cache['cache_expire'])
			return $this->destroyData($index);

		return $cache['data'];
	}

	/**
	 * Public interface, stores data in cache.
	 * @param string $index - The cache index to store the data under.
	 * @param mixed $data - The data to store.
	 * @param integer $ttl - The lifespan of the cached data, in seconds.  Leave empty or set as 0 to disable cache timeout.
	 * @return void
	 */
	public function storeData($index, $data, $ttl = 0)
	{
		// build the cache, with data and ttl expiry included
		$this->getEngine()->store($index, $this->getEngine()->build(array(
			'data'			=> $data,
			'cache_expire'	=> ($ttl) ? time() + (int) $ttl : 0,
		)));
	}

	/**
	 * Public interface, destroys the specified cache index.
	 * @param string $index - The cache index to destroy.
	 * @return NULL
	 */
	public function destroyData($index)
	{
		$this->getEngine()->destroy($index);

		return NULL;
	}
}
