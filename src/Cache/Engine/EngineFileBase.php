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
use OpenFlame\Framework\Exception\Cache\Engine\EngineFileBase as EngineFileBaseException;

if(!defined('OpenFlame\\Framework\\ROOT_PATH')) exit;

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
	 * Constructor
	 * @param array $properties - Array of properties to set for the various options in the engine.
	 * @return void
	 */
	public function __construct(array $properties)
	{
		if(isset($properties['cache_path']))
			$this->setCachePath($properties['cache_path']);
	}

	/**
	 * Set the cache file's path.
	 * @param string $path - The path to store cache files in.
	 * @return OpenFlame\Framework\Cache\Engine\EngineFileBase - Provides a fluent interface.
	 *
	 * @throws OpenFlame\Framework\Exception\Cache\Engine\EngineFileBase
	 */
	public function setCachePath($path)
	{
		if(!is_dir($path))
			throw new EngineFileBaseException(sprintf('The cache path "%1$s" is not a directory or does not exist', $path), EngineFileBaseException::ERR_CACHE_PATH_NOT_DIR);
		if(!is_readable($path) || !is_writable($path))
			throw new EngineFileBaseException(sprintf('The cache path "%1$s" is not accessible', $path), EngineFileBaseException::ERR_CACHE_PATH_NO_ACCESS);

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
	 * @throws OpenFlame\Framework\Exception\Cache\Engine\EngineFileBase
	 */
	protected function readFile($file)
	{
		$file = $this->cache_path . basename($file);
		if(!@is_readable($file))
			throw new EngineFileBaseException(sprintf('Cache file "%1$s" is unreadable', $file), EngineFileBaseException::ERR_CACHE_UNREADABLE);
		if(!$f = @fopen($file, 'r'))
			throw new EngineFileBaseException(sprintf('fopen() call failed for cache file "%1$s"', $file), EngineFileBaseException::ERR_CACHE_FOPEN_FAILED);
		if(@flock($f, LOCK_EX))
		{
			$data = @fread($f, @filesize($file));
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new EngineFileBaseException(sprintf('flock() call failed for cache file "%1$s"', $file), EngineFileBaseException::ERR_CACHE_FLOCK_FAILED);
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
	 * @throws OpenFlame\Framework\Exception\Cache\Engine\EngineFileBase
	 */
	protected function writeFile($file, $data)
	{
		$file = $this->cache_path . basename($file);
		if(@file_exists($file) && !@is_writable($file))
			throw new EngineFileBaseException(sprintf('Cache file "%1$s" is unwritable', $file), EngineFileBaseException::ERR_CACHE_UNWRITABLE);
		if(!$f = @fopen($file, 'w'))
			throw new EngineFileBaseException(sprintf('fopen() call failed for cache file "%1$s"', $file), EngineFileBaseException::ERR_CACHE_FOPEN_FAILED);
		if(@flock($f, LOCK_EX))
		{
			$length = @fwrite($f, $data);
			if($length !== strlen($data))
				throw new EngineFileBaseException(sprintf('fwrite() call failed for cache file "%1$s"', $file), EngineFileBaseException::ERR_CACHE_FWRITE_FAILED);
			@flock($f, LOCK_UN);
		}
		else
		{
			throw new EngineFileBaseException(sprintf('flock() call failed for cache file "%1$s"', $file), EngineFileBaseException::ERR_CACHE_FLOCK_FAILED);
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
