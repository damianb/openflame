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

namespace emberlabs\openflame\Cache\Engine\File;
use \emberlabs\openflame\Core\Internal\DirectoryException;
use \emberlabs\openflame\Core\Internal\FileException;
use \emberlabs\openflame\Event\Instance as Event;

/**
 * OpenFlame Framework - File-based cache engine base class,
 * 		Cache engine prototype, provides some common methods for all file-based engines to use.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
abstract class FileEngineBase
{
	/**
	 * @var string - The path to where cache files will be stored, if we are using a file-based cache engine.
	 */
	protected $cache_path = '';

	/**
	 * Gets the current cache path.
	 * @return string - Current cache path.
	 */
	public function getCachePath()
	{
		return $this->cache_path;
	}

	/**
	 * Set the cache file's path.
	 * @param string $path - The path to store cache files in.
	 * @return \emberlabs\openflame\Cache\Engine\File\FileEngineBase - Provides a fluent interface.
	 *
	 * @throws DirectoryException
	 */
	public function setCachePath($path)
	{
		if(!is_dir($path))
		{
			throw new DirectoryException(sprintf('The cache path "%1$s" is not a directory or does not exist', $path));
		}
		if(!is_readable($path) || !is_writable($path))
		{
			throw new DirectoryException(sprintf('The cache path "%1$s" is not accessible', $path));
		}

		$this->cache_path = rtrim($path, '/') . '/'; // ensure that the path has a trailing slash
		return $this;
	}

	/**
	 * Build the data for the cache file (and integrate in TTL checking)
	 * @param mixed $data - The data to cache.
	 * @param integer $ttl - The lifespan of the cached data, in seconds.  Leave empty or set as 0 to disable cache timeout.
	 * @return The data to store in the cache file.
	 */
	final public function build($data, $ttl)
	{
		return $this->engineBuild(array(
			'data'			=> $data,
			'cache_expire'	=> ($ttl != NULL && $ttl > 0) ? time() + (int) $ttl : 0,
		));
	}

	/**
	 * Load data from a cache file, with TTL checking integrated.
	 * @param string $key - The cache index to grab the data out of.
	 * @return mixed - The previously cached data, or NULL if the data is out of date.
	 */
	final public function load($key)
	{
		$cache = $this->engineLoad($key);

		// handle ttl checking here
		if(isset($cache['cache_expire']) && $cache['cache_expire'] != 0 && time() > $cache['cache_expire'])
		{
			$this->destroy($key);

			return NULL;
		}

		return $cache['data'];
	}

	abstract protected function engineBuild($data);
	abstract protected function engineLoad($key);

	/**
	 * Checks to see if a specified cache file exists.
	 * @param string $file - The name of the cache file.
	 * @return boolean - Does the file exist?
	 */
	protected function fileExists($file)
	{
		return file_exists($this->cache_path . basename($file));
	}

	/**
	 * Reads a specified cache file's contents.
	 * @param string $file - The file to read from.
	 * @return string - The file's data
	 *
	 * @throws FileException
	 */
	protected function readFile($file)
	{
		$file = $this->cache_path . basename($file);
		if(!@is_readable($file))
		{
			throw new FileException(sprintf('Cache file "%1$s" is unreadable', $file));
		}
		if(!$f = @fopen($file, 'r'))
		{
			throw new FileException(sprintf('fopen() call failed for cache file "%1$s"', $file));
		}

		if(@flock($f, LOCK_EX))
		{
			$data = @fread($f, @filesize($file));
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new FileException(sprintf('flock() call failed for cache file "%1$s"', $file));
		}

		@fclose($f);

		return $data;
	}

	/**
	 * Writes data to a specified cache file
	 * @param string $file - The file to write to.
	 * @param string $data - The data to write to the cache file.
	 * @return void
	 *
	 * @throws FileException
	 */
	protected function writeFile($file, $data)
	{
		$file = $this->cache_path . basename($file);
		if(@file_exists($file) && !@is_writable($file))
		{
			throw new FileException(sprintf('Cache file "%1$s" is unwritable', $file));
		}
		if(!$f = @fopen($file, 'w'))
		{
			throw new FileException(sprintf('fopen() call failed for cache file "%1$s"', $file));
		}

		if(@flock($f, LOCK_EX))
		{
			$length = @fwrite($f, $data);
			if($length !== strlen($data))
			{
				throw new FileException(sprintf('fwrite() call failed for cache file "%1$s"', $file));
			}
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new FileException(sprintf('flock() call failed for cache file "%1$s"', $file));
		}
		@fclose($f);
	}

	/**
	 * Deletes a specified file in the cache.
	 * @param string $file - The file to delete.
	 * @return boolean - Was the deletion successful?
	 */
	protected function deleteFile($file)
	{
		return @unlink($this->cache_path . basename($file));
	}

	abstract protected function getFileExtension();

	/**
	 * Garbage collection, goes through the cache and cleans up expired cache files
	 * @param \emberlabs\openflame\Event\Instance - Event instance (so this can be used as a listener)
	 * @return void
	 */
	public function gc(Event $event = NULL)
	{
		$now = time();
		$fileext = $this->getFileExtension();

		foreach(glob($this->cache_path . '*.' . $fileext . '.tmp') as $file)
		{
			$cache_name = substr(basename($file), 0, strlen($file) - strlen(".$fileext.tmp"));
			$cache = $this->engineLoad($cache_name);

			if(isset($cache['cache_expire']) && $cache['cache_expire'] != 0 && time() > $cache['cache_expire'])
			{
				$this->destroy($key);
			}
		}
	}
}
