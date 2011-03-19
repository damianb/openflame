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
 * OpenFlame Web Framework - File-based cache engine base class,
 * 		Cache engine prototype, provides some common methods for all file-based engines to use.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
abstract class EngineFileBase
{
	/**
	 * @var string - The path to where cache files will be stored, if we are using a file-based cache engine.
	 */
	protected $cache_path = '';

	/**
	 * Do we want to use a TTL check for this cache engine?
	 * @return true - Filecache-based engines don't implement TTL themselves.
	 */
	public function useTTLCheck()
	{
		return true;
	}

	/**
	 * Set the cache file's path.
	 * @param string $path - The path to store cache files in.
	 * @return OpenFlame\Framework\Cache\Engine\EngineFileBase - Provides a fluent interface.
	 *
	 * @throws \LogicException
	 * @throws \RuntimeException
	 */
	public function setCachePath($path)
	{
		if(!is_dir($path))
		{
			throw new \LogicException(sprintf('The cache path "%1$s" is not a directory or does not exist', $path));
		}
		if(!is_readable($path) || !is_writable($path))
		{
			throw new \RuntimeException(sprintf('The cache path "%1$s" is not accessible', $path));
		}

		$this->cache_path = rtrim($path, '/') . '/'; // ensure that the path has a trailing slash
		return $this;
	}

	/**
	 * Gets the current cache path.
	 * @return string - Current cache path.
	 */
	public function getCachePath()
	{
		return $this->cache_path;
	}

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
	 * @throws \RuntimeException
	 */
	protected function readFile($file)
	{
		$file = $this->cache_path . basename($file);
		if(!@is_readable($file))
		{
			throw new \RuntimeException(sprintf('Cache file "%1$s" is unreadable', $file));
		}
		if(!$f = @fopen($file, 'r'))
		{
			throw new \RuntimeException(sprintf('fopen() call failed for cache file "%1$s"', $file));
		}

		if(@flock($f, LOCK_EX))
		{
			$data = @fread($f, @filesize($file));
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new \RuntimeException(sprintf('flock() call failed for cache file "%1$s"', $file));
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
	 * @throws \RuntimeException
	 */
	protected function writeFile($file, $data)
	{
		$file = $this->cache_path . basename($file);
		if(@file_exists($file) && !@is_writable($file))
		{
			throw new \RuntimeException(sprintf('Cache file "%1$s" is unwritable', $file));
		}
		if(!$f = @fopen($file, 'w'))
		{
			throw new \RuntimeException(sprintf('fopen() call failed for cache file "%1$s"', $file));
		}

		if(@flock($f, LOCK_EX))
		{
			$length = @fwrite($f, $data);
			if($length !== strlen($data))
			{
				throw new \RuntimeException(sprintf('fwrite() call failed for cache file "%1$s"', $file));
			}
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new \RuntimeException(sprintf('flock() call failed for cache file "%1$s"', $file));
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
}
