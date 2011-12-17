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
use \OpenFlame\Framework\Session\Internal\SessionException;
use \OpenFlame\Framework\Session\Internal\StorageEngineException;
use \OpenFlame\Framework\Event\Instance as Event;

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
	 * @throws StorageEngineException
	 */
	public function init(array &$options)
	{
		$defaults = array(
			'filesystem.savepath'	=> ini_get('session.save_path'),
			'filesystem.prefix'		=> 'sess_',
			'filesystem.maxfileage'	=> (int) $options['session.expire'],
			'filesystem.ext'		=> 'tmp',
		);

		$this->options = array_merge($defaults, $options);

		// Force trailing slash
		$this->options['filesystem.savepath'] = rtrim($this->options['filesystem.savepath'], '/\\') . '/';

		// Do some basic checks on our filesystem
		if (!file_exists($this->options['filesystem.savepath']))
		{
			throw new StorageEngineException("The session file storage path does not exist.");
		}
		else if (!is_readable($this->options['filesystem.savepath']) || !is_writable($this->options['filesystem.savepath']))
		{
			throw new StorageEngineException("Could write to the session file storage directory.");
		}
	}

	/*
	 * Load data associated with the SID
	 * @param string sid - Session ID (Must be [a-z0-9])
	 * @return mixed - Arbitrary data stored
	 */
	public function load($sid)
	{
		$filepath = $this->makeFilepath($sid);
		$data = array();

		if (is_file($filepath))
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
		$result = file_put_contents($this->makeFilepath($sid), serialize($data));

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
		return unlink($this->makeFilepath($sid));
	}

	/*
	 * Little shortcut to centralize the filename creation
	 * @ignore
	 */
	private function makeFilepath($sid)
	{
		return $this->options['filesystem.savepath'] . $this->options['filesystem.prefix'] . $sid . '.' . $this->options['filesystem.ext'];
	}

	/*
	 * Garbage Collection
	 * Called at the end of each page load.
	 * @param \OpenFlame\Framework\Event\Instance $event - Event instance (so this can be used as a listener)
	 * @return void
	 */
	public function gc(Event $event = NULL)
	{
		$now = time();

		foreach(glob("{$this->options['filesystem.savepath']}*.{$this->options['filesystem.ext']}") as $file)
		{
			if ($this->options['filesystem.prefix'] != substr(basename($file), 0, strlen($this->options['filesystem.prefix'])))
			{
				continue;
			}

			if (filemtime($file) + $this->options['filesystem.maxfileage'] < $now)
			{
				// Taking out the trash
				unlink($file);
			}
		}
	}
}
