<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  dependency
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Dependency;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Framework - Dependency injector
 * 	     Provides fluid dependency injection.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Injector
{
	/**
	 * @var array - Array of closures which prepare the requested instance on demand.
	 */
	protected $injectors = array();

	/**
	 * @var \OpenFlame\Framework\Dependency\Injector - Singleton instance of the dependency injector
	 */
	protected static $instance;

	/**
	 * Constructor
	 * @return void
	 */
	protected function __construct() { }

	/**
	 * Get the singleton instance of the dependency injector.
	 * @return \OpenFlame\Framework\Dependency\Injector - Singleton instance of the dependency injector
	 */
	public static function getInstance()
	{
		if(self::$instance === NULL)
		{
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Get the injector closure.
	 * @param string $name - The name of the component to grab the injector for.
	 * @return \Closure - Returns the dependency injector closure to use.
	 *
	 * @throws \LogicException
	 */
	protected function getInjector($name)
	{
		if(!isset($this->injectors[$name]))
		{
			throw new \LogicException(sprintf('Cannot fetch dependency object "%s", no injector defined', $name));
		}
		return $this->injectors[$name];
	}

	/**
	 * Register a new dependency injector closure.
	 * @param string $name - The name of the dependency
	 * @param \Closure $injector - The closure to use when injecting the dependency
	 * @return \OpenFlame\Framework\Dependency\Injector - Provides a fluent interface.
	 */
	public function setInjector($name, \Closure $injector)
	{
		$this->injectors[$name] = $injector;

		return $this;
	}

	/**
	 * Trigger the dependency injector and store a reference to the resulting object in the OpenFlame core
	 * @param string $name - The name of the dependency to inject.
	 * @return object - The object that we are injecting.
	 */
	protected function fireInjector($name)
	{
		$injector = $this->getInjector($name);
		return \OpenFlame\Framework\Core::setObject($name, $injector());
	}

	/**
	 * Get a dependency (and fire the injector if the dependency has not been instantiated)
	 * @param string $name - The name of the dependency to inject.
	 * @return object - The object we are injecting.
	 */
	public function get($name)
	{
		$object = \OpenFlame\Framework\Core::getObject($name);
		if($object === NULL)
		{
			$object = $this->fireInjector($name);
		}

		return $object;
	}
}
