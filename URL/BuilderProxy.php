<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  url
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\URL;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Framework - Template proxy object for internal URL construction,
 * 	     Provides a near-seamless method of generating internal URLs inside of templates.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class BuilderProxy
{
	/**
	 * @var \OpenFlame\Framework\URL\Builder - The URL builder object.
	 */
	protected $builder;

	/**
	 * @var string - The string containing the extra GET data to append, if any.
	 */
	protected $extra_get_data = false;

	/**
	 * Constructor
	 * @param \OpenFlame\Framework\URL\Builder $manager - The URL builder object.
	 * @return void
	 */
	public function __construct(\OpenFlame\Framework\URL\Builder $builder)
	{
		$this->builder = $builder;
	}

	/**
	 * Magic proxy method for generating URLs, uses URL patterns with printf formats to resolve the URL to use.
	 * @param string $name - The method name called (used to figure out the pattern to use for URL generation).
	 * @param string $arguments - The arguments to pass to the vsprintf() call as the variable components of the URL pattern.
	 * @return string - Returns an empty string if no URL pattern specified, or returns a string containing the generated URL.
	 */
	public function __call($name, array $arguments)
	{
		$pattern = $this->builder->getPattern($name);
		// NULL pattern? No URL to generate.
		if($pattern === NULL)
		{
			return '';
		}

		$url = $this->builder->getBaseURL() . '/' . vsprintf($pattern, $arguments);

		if($this->extra_get_data === false)
		{
			// Handle appending extra GET data (for things like CSRF tokens, session IDs, etc.)
			$extra_params = $this->builder->getGlobalGetData();

			if($extra_params !== '')
			{
				$get_string = array();
				foreach($extra_params as $name => $value)
				{
					$get_string[] = $name . '=' . rawurlencode($value);
				}
				$extra_params = implode('&', $get_string);
			}

			$this->extra_get_data = $extra_params;
		}

		if($this->extra_get_data !== '')
		{
			$url .= (strpos($url, '?') !== false) ? rtrim($url, '&') . '&' . $this->extra_get_data : '?' . $this->extra_get_data;
		}

		return $url;
	}
}
