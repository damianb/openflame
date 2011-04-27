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

namespace OpenFlame\Framework\Event;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Event dispatcher object
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

		$priority = (int) $priority;
		if($priority > 20)
		{
			$priority = 20;
		}
		elseif($priority < -20)
		{
			$priority = -20;
		}

		$this->listeners[$event_type][$priority][] = $listener;

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

		// Ensure the listener priorities are in order
		ksort($this->listeners[$event->getName()]);
		foreach($this->listeners[$event->getName()] as $priority => $priority_thread)
		{
			for($i = 0, $size = sizeof($priority_thread); $i <= $size - 1; $i++)
			{
				$listener_callback = $priority_thread[$i];
				try
				{
					$return = call_user_func($listener_callback, $event);
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
	 * Dispatch an event to registered listeners, and checking to see if a listener wants to abort a
	 * @param \OpenFlame\Framework\Event\Instance $event - The event to dispatch.
	 * @return \OpenFlame\Framework\Event\Instance - The event dispatched.
	 */
	public function triggerUntilBreak(\OpenFlame\Framework\Event\Instance $event)
	{
		if(!$this->hasListeners($event->getName()))
		{
			return $event;
		}

		// Ensure the listener priorities are in order
		ksort($this->listeners[$event->getName()]);
		foreach($this->listeners[$event->getName()] as $priority => $priority_thread)
		{
			for($i = 0, $size = sizeof($priority_thread); $i <= $size - 1; $i++)
			{
				$listener_callback = $priority_thread[$i];
				try
				{
					$return = call_user_func($listener_callback, $event);
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
}
