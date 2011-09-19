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

	/**
	 * @var array - Array of "unsorted" listeners
	 */
	protected $unsorted = array();

	/**#@+
	 * @var integer - Constants representing the type of listener being interacted with.
	 */
	const LISTENER_CLOSURE = 1;
	const LISTENER_FUNCTION = 2;
	const LISTENER_STATIC_METHOD = 3;
	const LISTENER_CALL_USER_FUNC = 4;
	/**#@-*/

	/**#@+
	 * @var integer - Constants representing the type of trigger mechanism to use.
	 */
	const TRIGGER_NOBREAK = 1;
	const TRIGGER_MANUALBREAK = 2;
	const TRIGGER_RETURNBREAK = 3;
	/**#@-*/

	/**
	 * Register a new listener with the dispatcher
	 * @param string $event_name - The name of the event to attach the listener to.
	 * @param integer $priority - The priority for the listener to be registered as, similar to *nix "nice" values for CPU processes (-20 top priority, 20 bottom priority)
	 * @param callable $listener - The callable reference for the listener.
	 * @return \OpenFlame\Framework\Event\Dispatcher - Provides a fluent interface.
	 */
	public function register($event_name, $priority, $listener)
	{
		if(!isset($this->listeners[$event_name]) || !is_array($this->listeners[$event_name]))
		{
			$this->listeners[$event_name] = array();
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
		elseif(!is_array($listener) && function_exists($listener))
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

		$this->listeners[$event_name][$priority][] = array(
			'listener'		=> $listener,
			'type'			=> $listener_type,
		);

		// Flag this event as needing a sort before the next event dispatch
		$this->unsorted[$event_name] = true;

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
	 * @param integer $dispatch_type - The type of dispatch method to use (run all listeners, allow listeners to trigger break, break on a non-NULL return value)
	 * @return \OpenFlame\Framework\Event\Instance - The event dispatched.
	 */
	public function trigger(\OpenFlame\Framework\Event\Instance $event, $dispatch_type = self::TRIGGER_NOBREAK)
	{
		$event_name = $event->getName();

		// Check to see if this event has ANY listeners - if it doesn't, just bail out.
		if(!$this->hasListeners($event_name))
		{
			return $event;
		}

		// On-the-fly priority sorting
		if(isset($this->unsorted[$event_name]))
		{
			ksort($this->listeners[$event_name]);
			unset($this->unsorted[$event_name]);
		}

		foreach($this->listeners[$event_name] as $priority => $priority_thread)
		{
			foreach($priority_thread as $listener_entry)
			{
				$listener = $listener_entry['listener'];
				$listener_type = $listener_entry['type'];

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
					if($dispatch_type = self::TRIGGER_RETURNBREAK)
					{
						return $event; // PHP 5.4 compat -- cannot use "break (int)" anymore, so we just return the $event
					}
				}

				if($dispatch_type = self::TRIGGER_MANUALBREAK && $event->wasBreakTriggered())
				{
					return $event; // PHP 5.4 compat -- cannot use "break (int)" anymore, so we just return the $event
				}
			}
		}

		return $event;
	}

	/**
	 * Dispatch an event to registered listeners, and checking to see if a listener wants to abort (and if so, break)
	 * @param \OpenFlame\Framework\Event\Instance $event - The event to dispatch.
	 * @return \OpenFlame\Framework\Event\Instance - The event dispatched.
	 *
	 * @deprecated since 1.2.0
	 */
	public function triggerUntilBreak(\OpenFlame\Framework\Event\Instance $event)
	{
		trigger_error('\\OpenFlame\\Framework\\Event\\Dispatcher->triggerUntilBreak() is deprecated', E_USER_DEPRECATED);
		return $this->trigger($event, self::TRIGGER_MANUALBREAK);
	}

	/**
	 * Dispatch an event to registered listeners, and checking to see if a listener returned a value yet or not (and if so, break)
	 * @param \OpenFlame\Framework\Event\Instance $event - The event to dispatch.
	 * @return \OpenFlame\Framework\Event\Instance - The event dispatched.
	 *
	 * @deprecated since 1.2.0
	 */
	public function triggerUntilReturn(\OpenFlame\Framework\Event\Instance $event)
	{
		trigger_error('\\OpenFlame\\Framework\\Event\\Dispatcher->triggerUntilReturn() is deprecated', E_USER_DEPRECATED);
		return $this->trigger($event, self::TRIGGER_RETURNBREAK);
	}
}
