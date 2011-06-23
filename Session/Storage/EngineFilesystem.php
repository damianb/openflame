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
 * OpenFlame Framework - Sessions over the Filesystem,
 * 		(re)impementes the PHP native sessions to be abstracted for OpenFlame framework.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class EngineFilesystem implements EngineInterface
{
	/*
	 * Holds all configuration options
	 */
	protected $options = array();

	/*
	 * Holds session data prior to its departure into the driver
	 */
	private $data = array();

	/*
	 * Stores the session id
	 */
	private $sid = '';

	/*
	 */
	protected $now = 0;

	/*
	 * Init
	 *
	 * Called when the session object is created but before the session has started
	 * @return void
	 */
	public function init($options)
	{
		$this->options['file.savepath'] = isset($options['file.savepath']) ? $options['file.savepath'] : ini_get('upload_tmp_dir');
		$this->options['file.prefix'] = isset($options['file.prefix']) ? $options['file.prefix'] : 'sess_';
		$this->options['file.randseed'] = isset($options['file.randseed']) ? $options['file.randseed'] : chr(64 + mt_rand(1,26));
		$this->options['file.gctime'] = isset($options['file.gctime']) ? (int) $options['file.gctime'] : $options['session.expiretime'];

		if(substr($this->options['file.savepath'], -1) != '/' || substr($this->options['file.savepath'], -1) != '\\')
		{
			$this->options['file.savepath'] .= '/';
		}

		$this->now = time();
	}

	/*
	 * New Session
	 *
	 * Called when a new session needs to be created
	 * @param bool - Clear the session data? Useful to set true when a session does not validate
	 * @return string - New SID
	 */
	public function newSession($clearData = false)
	{
		if(empty($this->filename))
		{
			$this->filename = $this->options['file.savepath'] . $this->options['file.prefix'] . $this->sid . '.php';
		}

		$this->deleteSession();

		$this->sid = hash('sha1', $this->filename . $this->options['file.randseed']);
		$this->filename = $this->options['file.savepath'] . $this->options['file.prefix'] . $this->sid . '.php';

		if($clearData)
		{
			$this->data = array();
		}

		return $this->sid;
	}

	/*
	 * Delete Session
	 *
	 * Deletes the currently loaded session
	 * @return void
	 */
	public function deleteSession()
	{
		if(empty($this->filename))
		{
			$this->filename = $this->options['file.savepath'] . $this->options['file.prefix'] . $this->sid . '.php';
		}

		if(file_exists($this->filename))
		{
			unlink($this->filename);
		}
	}

	/*
	 * Session Data
	 *
	 * Load the session for use by the driver
	 * @return bool - True if a session was found, false if not
	 */
	public function loadSession($sid)
	{
		$this->sid = $sid;
		$this->filename = $this->options['file.savepath'] . $this->options['file.prefix'] . $this->sid . '.php';

		if(file_exists($this->filename))
		{
			list($ts, $data) = explode("\n", file_get_contents($this->filename, NULL, NULL, 15));
			$this->data = unserialize($data);
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	 * Load Data
	 *
	 * Get the current session data
	 * @return array - complex array of identical structure to one being stored
	 */
	public function loadData()
	{
		return sizeof($this->data) ? $this->data : array();
	}

	/*
	 * Store Session Data
	 *
	 * @param array - complex array
	 * @return void
	 */
	public function storeData($data)
	{
		$this->data = $data;

		file_put_contents($this->options['file.savepath'] . $this->options['file.prefix'] . $this->sid . '.php',
			"<?php exit; ?>\n{$this->now}\n" . serialize($this->data) . "\n");
	}

	/*
	 * Garbage collection
	 * Should be called periodically
	 */
	public function gc()
	{
		$files = scandir($this->options['file.savepath']);
		$cutoff = time() - $this->options['file.gctime'];

		foreach($files as $file)
		{
			$fullpath = $this->options['file.savepath'] . $file;

			if (substr($file, 0, strlen($this->options['file.prefix'])) == $this->options['file.prefix'])
			{
				// If the date in the older than the cutoff, /dev/null it goes.
				if(reset(explode("\n", file_get_contents($fullpath, NULL, NULL, 15))) < $cutoff)
				{
					unlink($fullpath);
				}
			}
		}
	}
}
