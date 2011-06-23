<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  event
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Event;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Framework - Event dispatcher object
 * 	     Provides event dispatcher functionality for ease of extensibility.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Dispatcher
{
	/**
	 * @var array - Our array of stored listeners and any extra data.
	 */
	protected $listeners = array();

	/**#@+
	 * @var integer - Constants representing the type of listener being interacted with.
	 */
	const LISTENER_CLOSURE = 1;
	const LISTENER_FUNCTION = 2;
	const LISTENER_STATIC_METHOD = 3;
	const LISTENER_CALL_USER_FUNC = 4;
	/**#@-*/

	/**
	 * Register a new listener with the dispatcher
	 * @param string $event_type - The type of event type to attach the listener to.
	 * @param integer $priority - The priority for the listener to be registered as, similar to *nix "nice" values for CPU processes (-20 top priority, 20 bottom priority)
	 * @param callable $listener - The callable reference for the listener.
	 * @return \OpenFlame\Framework\Event\Dispatcher - Provides a fluent interface.
	 */
	public function register($event_type, $priority, $listener)
	{
		if(!isset($this->listeners[$event_type]) || !is_array($this->listeners[$event_type]))
		{
			$this->listeners[$event_type] = array();
		}

		// Handle priority settings (a la UNIX nice values)
		$priority = (int) $priority;
		if($priority > 20)
		{
			$priority = 20;
		}
		elseif($priority < -20)
		{
			$priority = -20;
		}

		// Check to see what type of listener we're dealing with here; this allows us to use some shortcuts down the road.
		$listener_type = NULL;
		if($listener instanceof \Closure)
		{
			// It's a closure!  <3
			$listener_type = self::LISTENER_CLOSURE;
		}
		elseif(function_exists($listener))
		{
			$listener_type = self::LISTENER_FUNCTION;
		}
		elseif(is_string($listener) && sizeof(explode('::', $listener, 2)) > 1) // checking to see if we're actually using a static call and doing so properly
		{
			$listener = explode('::', $listener, 2);
			$listener_type = self::LISTENER_STATIC_METHOD;
		}
		else
		{
			// Worst case scenario.  We HAVE to use call_user_func() now.
			$listener_type = self::LISTENER_CALL_USER_FUNC;
		}

		$this->listeners[$event_type][$priority][] = array(
			'listener'		=> $listener,
			'type'			=> $listener_type,
		);

		// Ensure the listener priorities are in order
		ksort($this->listeners[$event_type]);

		return $this;
	}

	/**
	 * Check to see if an event has any listeners registered to it
	 * @param string $event_type - The type of event to check.
	 * @return boolean - Does the event have listeners attached?
	 */
	public function hasListeners($event_type)
	{
		return !empty($this->listeners[$event_type]);
	}

	/**
	 * Dispatch an event to registered listeners
	 * @param \OpenFlame\Framework\Event\Instance $event - The event to dispatch.
	 * @return \OpenFlame\Framework\Event\Instance - The event dispatched.
	 */
	public function trigger(\OpenFlame\Framework\Event\Instance $event)
	{
		if(!$this->hasListeners($event->getName()))
		{
			return $event;
		}

		foreach($this->listeners[$event->getName()] as $priority => $priority_thread)
		{
			for($i = 0, $size = sizeof($priority_thread); $i <= $size - 1; $i++)
			{
				$listener = $priority_thread[$i]['listener'];
				$listener_type = $priority_thread[$i]['type'];

				try
				{
					// Use faster, quicker methods than call_user_func() for triggering listeners if they're available
					switch($listener_type)
					{
						case self::LISTENER_CLOSURE:
						case self::LISTENER_FUNCTION:
							$return = $listener($event);
							break;

						case self::LISTENER_STATIC_METHOD:
							list($class, $method) = $listener;
							$return = $class::$method($event);
							break;

						case self::LISTENER_CALL_USER_FUNC:
						default:
							$return = call_user_func($listener, $event);
							break;
					}

					if($return !== NULL)
					{
						$event->setReturn($return);
					}
				}
				catch(\Exception $e)
				{
					throw new \RuntimeException(sprintf('Exception encountered in event listener assigned to event "%1$s"', $event->getName()), 0, $e);
				}
			}
		}

		return $event;
	}

	/**
	 * Dispatch an event to registered listeners, and checking to see if a listener wants to abort (and if so, break)
	 * @param \OpenFlame\Framework\Event\Instance $event - The event to dispatch.
	 * @return \OpenFlame\Framework\Event\Instance - The event dispatched.
	 */
	public function triggerUntilBreak(\OpenFlame\Framework\Event\Instance $event)
	{
		if(!$this->hasListeners($event->getName()))
		{
			return $event;
		}

		foreach($this->listeners[$event->getName()] as $priority => $priority_thread)
		{
			for($i = 0, $size = sizeof($priority_thread); $i <= $size - 1; $i++)
			{
				$listener = $priority_thread[$i]['listener'];
				$listener_type = $priority_thread[$i]['type'];

				try
				{
					// Use faster, quicker methods than call_user_func() for triggering listeners if they're available
					switch($listener_type)
					{
						case self::LISTENER_CLOSURE:
						case self::LISTENER_FUNCTION:
							$return = $listener($event);
							break;

						case self::LISTENER_STATIC_METHOD:
							list($class, $method) = $listener;
							$return = $class::$method($event);
							break;

						case self::LISTENER_CALL_USER_FUNC:
						default:
							$return = call_user_func($listener, $event);
							break;
					}

					if($return !== NULL)
					{
						$event->setReturn($return);
					}
				}
				catch(\Exception $e)
				{
					throw new \RuntimeException(sprintf('Exception encountered in event listener assigned to event "%1$s"', $event->getName()), 0, $e);
				}

				if($event->wasBreakTriggered())
				{
					break 2; // break 2 so that we completely break out
				}
			}
		}

		return $event;
	}

	/**
	 * Dispatch an event to registered listeners, and checking to see if a listener returned a value yet or not (and if so, break)
	 * @param \OpenFlame\Framework\Event\Instance $event - The event to dispatch.
	 * @return \OpenFlame\Framework\Event\Instance - The event dispatched.
	 */
	public function triggerUntilReturn(\OpenFlame\Framework\Event\Instance $event)
	{
		if(!$this->hasListeners($event->getName()))
		{
			return $event;
		}

		foreach($this->listeners[$event->getName()] as $priority => $priority_thread)
		{
			for($i = 0, $size = sizeof($priority_thread); $i <= $size - 1; $i++)
			{
				$listener = $priority_thread[$i]['listener'];
				$listener_type = $priority_thread[$i]['type'];

				try
				{
					// Use faster, quicker methods than call_user_func() for triggering listeners if they're available
					switch($listener_type)
					{
						case self::LISTENER_CLOSURE:
						case self::LISTENER_FUNCTION:
							$return = $listener($event);
							break;

						case self::LISTENER_STATIC_METHOD:
							list($class, $method) = $listener;
							$return = $class::$method($event);
							break;

						case self::LISTENER_CALL_USER_FUNC:
						default:
							$return = call_user_func($listener, $event);
							break;
					}

					if($return !== NULL)
					{
						$event->setReturn($return);
					}
				}
				catch(\Exception $e)
				{
					throw new \RuntimeException(sprintf('Exception encountered in event listener assigned to event "%1$s"', $event->getName()), 0, $e);
				}

				if($return !== NULL)
				{
					break 2; // break 2 so that we completely break out
				}
			}
		}

		return $event;
	}
}
