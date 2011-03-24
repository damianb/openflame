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
 * OpenFlame Web Framework - Event object
 * 	     Represents a dispatched event.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Instance
{
	/**
	 * @var string - The event name.
	 */
	protected $name = '';

	/**
	 * @var array - Related event data
	 */
	protected $data = array();

	/**
	 * @var mixed - The source of the event, may be null.
	 */
	protected $source;

	/**
	 * @var boolean - Should the event tell the dispatcher to break out of the trigger cycle?
	 */
	protected $trigger_break = false;

	/**
	 * Create a new event, used as a one-line shortcut for quickly dispatching events.
	 * @param string $name - The event's name.
	 * @return \OpenFlame\Framework\Event\Instance - The event created.
	 */
	public static function newEvent($name)
	{
		$self = new static();
		$self->setName($name);
		return $self;
	}

	/**
	 * Get the name for the event
	 * @return string - The event's name.
	 */
	public function getName()
	{
		return (string) $this->name;
	}

	/**
	 * Set the name for the event.
	 * @param string $name - The name to set.
	 * @return \OpenFlame\Framework\Event\Instance - Provides a fluent interface.
	 */
	public function setName($name)
	{
		$this->name = (string) $name;
		return $this;
	}

	/**
	 * Get the source of the event.
	 * @return mixed - Returns the source of the event (an object) or NULL.
	 */
	public function getSource()
	{
		return $this->source;
	}

	/**
	 * Set the source of the event.
	 * @param mixed $source - The source of the event, must be an object or NULL.
	 * @return \OpenFlame\Framework\Event\Instance - Provides a fluent interface.
	 *
	 * @throws \InvalidArgumentException
	 */
	public function setSource($source)
	{
		if($source !== NULL && !is_object($source))
		{
			throw new \InvalidArgumentException('Source provided to event instance must be an object or NULL');
		}

		$this->source = $source;
		return $this;
	}

	/**
	 * Get the array of data attached to the event.
	 * @return array - The array of data attached to this event.
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Set the array of data to attach to this event.
	 * @param array $data - The array of data to attach.
	 * @return \Yukari\Event\Instance - Provides a fluent interface.
	 */
	public function setData(array $data = array())
	{
		$this->data = $data;
		return $this;
	}

	/**
	 * Check if a data point exists in this event.
	 * @param string - The key for the data point to grab.
	 * @return boolean - Does the data point exist?
	 */
	public function dataPointExists($point)
	{
		return (bool) array_key_exists($point, $this->data);
	}

	/**
	 * Get a single point of data attached to this event.
	 * @param string - The key for the data point to grab.
	 * @return mixed - The point of data we're looking for
	 *
	 * @throws \InvalidArgumentException
	 */
	public function getDataPoint($point)
	{
		if(!$this->dataPointExists($point))
		{
			throw new \InvalidArgumentException('Invalid event parameter specified');
		}
		return $this->data[$point];
	}

	/**
	 * Attach a single point of data to this event
	 * @param string $point - The key to attach the data under.
	 * @param mixed $value - The data to attach.
	 * @return \Yukari\Event\Instance - Provides a fluent interface.
	 */
	public function setDataPoint($point, $value)
	{
		$this->data[$point] = $value;
		return $this;
	}

	public function breakTrigger()
	{
		// asdf
	}

	public function wasBreakTriggered()
	{
		return (bool) $this->trigger_break;
	}
}
