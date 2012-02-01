<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  cache
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Cache\Engine;
use \emberlabs\openflame\Core\Internal\RequirementException;
use \emberlabs\openflame\Event\Instance as Event;

/**
 * OpenFlame Framework - APC cache engine class,
 * 		APC engine, provides streamlined access to apc cache storage.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class APCEngine implements EngineInterface
{
	/**
	 * @throws RequirementException
	 */
	public function __construct()
	{
		if(!function_exists('apc_fetch'))
		{
			throw new RequirementException('APC cache functions not available');
		}
	}

	/**
	 * Get the engine name.
	 * @return string - The engine name.
	 */
	public function getEngineName()
	{
		return 'APC';
	}

	/**
	 * Load data from APC.
	 * @param string $key - The cache key to grab the data out of.
	 * @return mixed - The previously cached data, or NULL if the data is out of date.
	 */
	public function load($key)
	{
		$success = false;
		$ret = apc_fetch($key, $success);
		if($ret === false && $success === false)
		{
			return NULL;
		}
		return $ret;
	}

	/**
	 * Check to see if a cache entry exists.
	 * @param string $key - The entry to check.
	 * @return boolean - Has the data been cached?
	 */
	public function exists($key)
	{
		return apc_exists($key);
	}

	/**
	 * Destroys a cache entry.
	 * @param string $key - The cache entry to destroy.
	 * @return void
	 */
	public function destroy($key)
	{
		return apc_delete($key);
	}

	/**
	 * Stores data to a cache entry.
	 * @param string $key - The cache entry to store our data in.
	 * @param string $data - The data to cache.
	 * @param integer $ttl - The lifespan of the cached data, in seconds.  Leave empty or set as 0 to disable cache timeout.
	 * @return void
	 */
	public function store($key, $data, $ttl)
	{
		apc_store($key, $data, $ttl);
	}

	/**
	 * Garbage collection, goes through the cache and cleans up expired cache entries
	 * @param \emberlabs\openflame\Event\Instance - Event instance (so this can be used as a listener)
	 * @return void
	 */
	public function gc(Event $event = NULL)
	{
		// apc handles this just fine on its own.  do nothing.
	}
}
