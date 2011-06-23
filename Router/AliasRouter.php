<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  router
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Router;
use OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Alias router,
 * 	     An extension to the router that provides dynamic routing capability.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class AliasRouter
{
	/**
	 * @var array - Array of route aliases
	 */
	protected $aliases = array();

	/**
	 * Registers a callback to an alias, to be fetched by the routeinstance when a route alias is used as the route callback.
	 * @param string $alias - The route alias to register to.
	 * @param mixed $callback - Either the callback to return, or a closure to execute to obtain the correct callback.
	 * @return \OpenFlame\Framework\Router\AliasRouter - Provides a fluent interface.
	 */
	public function registerAlias($alias, $callback)
	{
		$this->aliases[(string) $alias] = $callback;

		return $this;
	}

	/**
	 * Resolve and obtain the callback assigned to the specified alias.
	 * @param string $alias - The alias to obtain the callback for.
	 * @return string - The callback assigned to the specified alias.
	 *
	 * @throws \RuntimeException
	 */
	public function resolveAlias($alias)
	{
		if(!isset($this->aliases[(string) $alias]))
		{
			throw new \RuntimeException(sprintf('No route callback registered to route alias "%s"', $alias));
		}

		$callback = $this->aliases[(string) $alias];

		if($callback instanceof \Closure)
		{
			return $callback();
		}
		else
		{
			return $callback;
		}
	}
}
