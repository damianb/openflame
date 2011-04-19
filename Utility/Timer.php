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

namespace OpenFlame\Framework\Utility;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Benchmarking class,
 * 		Utility for tracking execution time throughout page execution.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note        This class should not be instantiated; it should only be statically accessed.
 */
class Timer
{
	/**
	 * @var float - Start time.
	 */
	protected $start = 0;

	/**
	 * @var array - Array of time mark points
	 */
	protected $time_markers = array();

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		$this->start = microtime(true);
		$this->time_markers['start'] = $this->start;
	}

	/**
	 * Mark a time point.
	 * @param string $mark_name - The time point's name.
	 * @return float - The current exec time.
	 */
	public function mark($mark_name)
	{
		$time = microtime(true);
		if(!isset($this->time_markers['_' . (string) $mark_name]))
		{
			$this->time_markers['_' . (string) $mark_name] = array();
		}
		$this->time_markers['_' . (string) $mark_name][] = $time;

		return round($this->start - $time, 5);
	}

	/**
	 * Get the start time
	 * @return float - The start time for the timer.
	 */
	public function getStart()
	{
		return $this->start;
	}

	/**
	 * Get the last time point under a specified mark name.
	 * @param string $mark_name - The time point's name.
	 * @return float - The exec time up until that time point was hit.
	 */
	public function getLastMark($mark_name)
	{
		return round($this->start - end($this->time_markers['_' . (string) $mark_name]), 5);
	}

	/**
	 * Get all recorded times under a specified mark name.
	 * @param string $mark_name - The time point's name.
	 * @return array - Array of time points recorded under the specified mark name.
	 */
	public function getMarks($mark_name)
	{
		return $this->time_markers['_' . (string) $mark_name];
	}

	/**
	 * Get all recorded times for all marks.
	 * @return array - All recorded time points.
	 */
	public function getAllMarks()
	{
		return $this->time_markers;
	}
}
