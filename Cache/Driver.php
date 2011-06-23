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

namespace OpenFlame\Framework\Cache;
use \OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Cache interface class,
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
	 * Get the cache engine currently in use.
	 * @return \OpenFlame\Framework\Cache\Engine\EngineInterface - The cache engine in use.
	 */
	public function getEngine()
	{
		return $this->engine;
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
	 * Public interface, checks to see if data has been cached already or not.
	 * @param $index - The index to check.
	 * @return boolean - Has the data been cached?
	 *
	 * @throws \LogicException
	 */
	public function dataCached($index)
	{
		if(empty($this->engine))
		{
			throw new \LogicException('Cache engine not loaded');
		}

		return $this->engine->exists($index);
	}

	/**
	 * Public interface, loads cached data from a given index, so long as it exists.
	 * @param string $index - The index to load cached data from.
	 * @return mixed - The previously cached data.
	 *
	 * @throws \LogicException
	 */
	public function loadData($index)
	{
		if(empty($this->engine))
		{
			throw new \LogicException('Cache engine not loaded');
		}

		// if data is not cached already, return NULL
		if(!$this->dataCached($index))
		{
			return NULL;
		}

		return $this->engine->load($index);
	}

	/**
	 * Public interface, stores data in cache.
	 * @param string $index - The cache index to store the data under.
	 * @param mixed $data - The data to store.
	 * @param integer $ttl - The lifespan of the cached data, in seconds.  Leave empty or set as 0 to disable cache timeout.
	 * @return void
	 *
	 * @throws \LogicException
	 */
	public function storeData($index, $data, $ttl = 0)
	{
		if(empty($this->engine))
		{
			throw new \LogicException('Cache engine not loaded');
		}

		// build the cache, with data and ttl expiry included
		$this->engine->store($index, $this->engine->build($data, $ttl));
	}

	/**
	 * Public interface, destroys the specified cache index.
	 * @param string $index - The cache index to destroy.
	 * @return NULL
	 */
	public function destroyData($index)
	{
		if(empty($this->engine))
		{
			throw new \LogicException('Cache engine not loaded');
		}

		$this->engine->destroy($index);

		return NULL;
	}
}
