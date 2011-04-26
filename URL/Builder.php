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
	protected $url_patterns = array();

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

	public function newPattern($pattern_name, $pattern)
	{
		$this->url_patterns[(string) $pattern_name] = ltrim($pattern, '/');

		return $this;
	}

	public function newPatterns(array $patterns)
	{
		foreach($patterns as $pattern_name => $pattern)
		{
			$this->newURLPattern($pattern_name, $pattern);
		}
	}

	public function getPattern($pattern_name)
	{
		if(!isset($this->url_patterns[(string) $pattern_name]))
		{
			return NULL;
		}

		return $this->url_patterns[(string) $pattern_name];
	}
}
