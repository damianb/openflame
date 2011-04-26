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

namespace OpenFlame\Framework\URL;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - URL abstraction and construction object,
 * 	     A cohesive method of generating internal URLs.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Builder
{
	/**
	 * @var array - The array of URL patterns.
	 */
	protected $url_patterns = array();

	/**
	 * @var string - The base URL to attach to all generated URLs.
	 */
	protected $base_url = '';

	/**
	 * Get the "base URL" of this installation, which is automatically added to all URLs.
	 * @return string - The base URL we are using.
	 */
	public function getBaseURL()
	{
		return $this->base_url;
	}

	/**
	 * Set the "base URL" for this installation, which will be added to all URLs.
	 * @param string $base_url - The "base URL" which we're going to strip
	 * @return \OpenFlame\Framework\URL\Builder - Provides a fluent interface.
	 */
	public function setBaseURL($base_url)
	{
		$this->base_url = '/' . ltrim(rtrim($base_url, '/'), '/'); // We don't want a trailing slash here, but we want to guarantee a leading slash.

		return $this;
	}

	/**
	 * Define a new printf() compatible pattern to use for internal URL generation.
	 * @param string $pattern_name - The name to give this pattern.
	 * @param string $pattern - The printf() compatible pattern to use.
	 * @return \OpenFlame\Framework\URL\Builder - Provides a fluent interface.
	 */
	public function newPattern($pattern_name, $pattern)
	{
		$this->url_patterns[(string) $pattern_name] = ltrim($pattern, '/'); // Ensure no trailing spaces!

		return $this;
	}

	/**
	 * Define a set of printf() compatible patterns to use for internal URL generation.
	 * @param array $patterns - An array of patterns to use, with the keys for each element being the pattern names for each pattern.
	 * @return \OpenFlame\Framework\URL\Builder - Provides a fluent interface.
	 */
	public function newPatterns(array $patterns)
	{
		foreach($patterns as $pattern_name => $pattern)
		{
			$this->newURLPattern($pattern_name, $pattern);
		}

		return $this;
	}

	/**
	 * Get a specific pattern by its pattern name
	 * @param string $pattern_name - The name of the pattern to get.
	 * @return mixed - NULL returned if no such pattern by that name, or the pattern string previously provided.
	 */
	public function getPattern($pattern_name)
	{
		if(!isset($this->url_patterns[(string) $pattern_name]))
		{
			return NULL;
		}

		return $this->url_patterns[(string) $pattern_name];
	}
}
