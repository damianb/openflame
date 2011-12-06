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
use \OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Event scheduler object, schedules tasks to be regularly triggered in the future.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Scheduler
{
	/**
	 * @var array - Array of currently defined tasks
	 */
	protected $tasks = array();

	/**
	 * @var array - Array containing the times that each task was last run
	 */
	protected $last_run = array();

	/**
	 * Define a new task to be triggered.
	 * @param string $task_name - The name of the task to trigger.
	 * @param integer $interval - The interval in seconds that this task should be triggered.
	 * @return \OpenFlame\Framework\Event\Scheduler - Provides a fluent interface.
	 */
	public function newTask($task_name, $interval)
	{
		$this->tasks[$task_name] = (int) $interval;

		return $this;
	}

	/**
	 * Remove a task from the schedule.
	 * @return \OpenFlame\Framework\Event\Scheduler - Provides a fluent interface.
	 */
	public function deleteTask($task_name)
	{
		unset($this->tasks[$task_name], $this->last_run[$task_name]);

		return $this;
	}

	/**
	 * Get the current task schedule to cache.
	 * @return array - The array of times that each task was last run.
	 */
	public function getScheduleCache()
	{
		return $this->last_run;
	}

	/**
	 * Load a task schedule from cache.
	 * @param array $cache - The cached task schedule to load.
	 * @return \OpenFlame\Framework\Event\Scheduler - Provides a fluent interface.
	 */
	public function loadScheduleCache(array $cache)
	{
		$this->last_run = $cache;

		return $this;
	}

	/**
	 * Get the tasks that are scheduled to be triggered.
	 * @param integer $now - The time to check against, defaults to the output of time().
	 * @return array - The array of tasks that are scheduled to be triggered.
	 */
	public function getScheduledTasks($now = NULL)
	{
		if($now === NULL)
		{
			$now = time();
		}

		$return = array();
		foreach($this->tasks as $task_name => $task_interval)
		{
			if(!isset($this->last_run[$task_name]))
			{
				$this->last_run[$task_name] = $now;
				continue;
			}

			if(($this->last_run[$task_name] + $task_interval) <= $now)
			{
				$return[] = $task_name;
			}
		}

		return $return;
	}

	/**
	 * Checks to see if any tasks are scheduled to be run now, and if any are, triggers their associated events.
	 * @return array - The array of tasks that were just run.
	 */
	public function runTasks()
	{
		$now = time();
		$injector = \OpenFlame\Framework\Dependency\Injector::getInstance();
		$dispatcher = $injector->get('dispatcher');

		$tasks_run = $this->getScheduledTasks($now);

		foreach($tasks_run as $task)
		{
			$dispatcher->trigger(\OpenFlame\Framework\Event\Instance::newEvent('task.' . $task_name), \OpenFlame\Framework\Event\Dispatcher::TRIGGER_MANUALBREAK);
			$this->last_run[$task_name] = $now;

			$tasks_run[] = $task_name;
		}

		return $tasks_run;
	}
}
