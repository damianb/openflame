<?php
/**
 *
 * @package OpenFlame Web Framework
 * @copyright (c) 2010 OpenFlameCMS.com
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 *
 * Minimum Requirement: PHP 5.2.0
 *
 * @uses OfJSON
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - JSON Cache class,
 * 		JSON cache engine for use with the cache interface.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 */
class OfCacheEngineJSON extends OfCacheEngineBase implements OfCacheEngineInterface
{
	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		// Make sure we have the JSON extension loaded
		//if(!class_exists('OfJSON')) // @todo replace with whatever include code we'll be using later
		//	include OF_ROOT . 'OfJSON.php';
	}

	/**
	 * Builds a JSON-based cache file, complete with idiot warning.
	 * @param mixed $data - The data to cache.
	 * @return string - Full JSON code to be stored in a cache file.
	 */
	public function build($data)
	{
		$data = OfJSON::encode($data);

		return implode(PHP_EOL, array(
			'# OpenFlame Web Framework cache file - modify at your own risk!',
			'# data md5 checksum: ' . hash('md5', $data),
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
		return OfJSON::decode($json);
	}

	/**
	 * Check to see if a cache file exists.
	 * @param string $file - The file to check.
	 * @return boolean - Has the data been cached?
	 */
	public function exists($file)
	{
		return $this->fileExists($file . '.json');
	}

	/**
	 * Destroys a cache file.
	 * @param string $file - The cache file to destroy.
	 * @return void
	 */
	public function destroy($file)
	{
		$this->deleteFile("$file.json");
	}

	/**
	 * Stores data to a cache file.
	 * @param string $file - The cache file to store our data in.
	 * @param string $data - The data to cache.
	 * @return void
	 */
	public function store($file, $data)
	{
		$this->writeFile("$file.json", $data);
	}
}
