<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  session
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Session\Storage;
use \OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Sessions Engine interface,
 * 		Sessions engine prototype, declares required methods that a sessions engine must define in order to be valid.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class EngineFilesystem implements EngineInterface
{
	/*
	 * Configuration Options
	 */
	private $options;

	/*
	 * Initialized the engine
	 * @param array options - Associative array of options
	 * @return void
	 */
	public function init(&$options)
	{
		$this->options['filesystem.cachepath'] = isset($options['filesystem.cachepath']) ? 
			$options['filesystem.cachepath'] : ini_get('session.save_path');
		
		$endChar = substr($this->options['filesystem.cachepath'], -1);
		if ($endChar != '/' || $endChar != '\\')
		{
			$this->options['filesystem.cachepath'] .= '/';
		}

		$this->options['filesystem.prefix'] = isset($options['filesystem.prefix']) ? 
			$options['filesystem.prefix'] : 'sess_';

		$this->options['filesystem.maxfileage'] = (isset($options['filesystem.maxfileage']) && ((int) $options['filesystem.maxfileage']) >= $options['session.expire']) ?
			(int) $options['filesystem.maxfileage'] : (int) $options['session.expire'];
	}

	/*
	 * Load data associated with the SID
	 * @param string sid - Session ID (Must be [a-z0-9])
	 * @return mixed - Arbitrary data stored 
	 */
	public function load($sid)
	{
		$filepath = $this->options['filesystem.cachepath'] . $this->options['filesystem.prefix'] . $sid;
		$data = array();

		if (file_exists($filepath))
		{
			$data = unserialize(file_get_contents($filepath));
		}

		return $data;
	}

	/*
	 * Store data associated with the session id
	 * @param string sid - Session ID (Must be [a-z0-9])
	 * @param mixed data - Arbitrary data to store
	 * @return bool - true on success, false on failure
	 */
	public function store($sid, $data)
	{
		$result = file_put_contents($this->options['filesystem.cachepath'] . $this->options['filesystem.prefix'] . $sid, serialize($data));

		return ($result !== false) ? true : false;
	}

	/*
	 * Purge session object
	 * Basically giving the Engine the signal to kill the data associated with 
	 * this session ID.
	 * @param string sid
	 */
	public function purge($sid)
	{
		return unlink($this->options['filesystem.cachepath'] . $this->options['filesystem.prefix'] . $sid);
	}

	/*
	 * Garbage Collection
	 * Called at the end of each page load.
	 * @param \OpenFlame\Framework\Event\Instance e - Event instance (so this can be used as a closure)
	 * @return void
	 */
	public function gc(\OpenFlame\Framework\Event\Instance $e = null)
	{
		$now = time();

		foreach(scandir($this->options['filesystem.cachepath']) as $file)
		{
			if ($file == '.' || 
				$file == '..' ||
				$this->options['filesystem.prefix'] != substr($file, 0, strlen($this->options['filesystem.prefix'])))
			{
				continue;
			}

			if (filemtime($this->options['filesystem.cachepath'] . $file) + $this->options['filesystem.maxfileage'] < $now)
			{
				// Takeing out the trash
				unlink($this->options['filesystem.cachepath'] . $file);
			}
		}
	}
}
