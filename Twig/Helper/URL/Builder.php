<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  url
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Twig\Helper\URL;

/**
 * OpenFlame Framework - URL abstraction and construction object,
 * 	     A cohesive method of generating internal URLs.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
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
	 * @var array - Array of extra GET vars to add to every URL.
	 */
	protected $extra_get_params = array();

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
	 * @return \emberlabs\openflame\Twig\Helper\URL\Builder - Provides a fluent interface.
	 */
	public function setBaseURL($base_url)
	{
		$this->base_url = '/' . ltrim(rtrim($base_url, '/'), '/'); // We don't want a trailing slash here, but we want to guarantee a leading slash.

		return $this;
	}

	/**
	 * Get the entire array of GET data elements to append to all generated URLs.
	 * @return array
	 */
	public function getGlobalGetData()
	{
		return $this->extra_get_params;
	}

	/**
	 * Define a new GET data element to append to all generated URLs.
	 * @param string $name - The name of the data element to add
	 * @param string $value - The value to use for the data.
	 * @return \emberlabs\openflame\Twig\Helper\URL\Builder - Provides a fluent interface.
	 */
	public function addGlobalGetVar($name, $value)
	{
		$this->extra_get_params[(string) $name] = $value;

		return $this;
	}

	/**
	 * Define a new printf() compatible pattern to use for internal URL generation.
	 * @param string $pattern_name - The name to give this pattern.
	 * @param string $pattern - The printf() compatible pattern to use.
	 * @return \emberlabs\openflame\Twig\Helper\URL\Builder - Provides a fluent interface.
	 */
	public function newPattern($pattern_name, $pattern)
	{
		$this->url_patterns[(string) $pattern_name] = ltrim($pattern, '/'); // Ensure no trailing spaces!

		return $this;
	}

	/**
	 * Define a set of printf() compatible patterns to use for internal URL generation.
	 * @param array $patterns - An array of patterns to use, with the keys for each element being the pattern names for each pattern.
	 * @return \emberlabs\openflame\Twig\Helper\URL\Builder - Provides a fluent interface.
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
