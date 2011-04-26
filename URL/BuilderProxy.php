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

	public function __construct(\OpenFlame\Framework\URL\Builder $builder)
	{
		$this->builder = $builder;
	}

	public function __call($name, array $arguments)
	{
		$pattern = $this->builder->getPattern($name);
		if($pattern === NULL)
		{
			return NULL;
		}

		return '/' . ltrim($this->builder->getBaseURL(), '/') . '/' . ltrim(vsprintf($pattern, $arguments), '/');
	}
}
