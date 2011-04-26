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
 * OpenFlame Web Framework - Template proxy object for internal URL construction,
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
		if($pattern === NULL)
		{
			return '';
		}

		return $this->builder->getBaseURL() . '/' . vsprintf($pattern, $arguments);
	}
}
