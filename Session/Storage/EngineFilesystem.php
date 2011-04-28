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

namespace OpenFlame\Framework\Session\Storage;
use \OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Sessions over the Filesystem,
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
	 * Init
	 *
	 * Called when the session object is created but before the session has started
	 * @return void
	 */
	public function init($options)
	{
		$this->options['savepath'] = isset($options['savepath']) ? $options['savepath'] : ini_get('upload_tmp_dir');
		$this->options['fileprefix'] = isset($options['fileprefix']) ? $options['fileprefix'] : 'sess_';
		$this->options['randseed'] = isset($options['randseed']) ? $options['randseed'] : chr(64 + mt_rand(1,26));
		$this->options['gctime'] = isset($options['gctime']) ? (int) $options['gctime'] : $options['expiretime'];
		
		if(substr($this->options['savepath'], -1) != '/' || substr($this->options['savepath'], -1) != '\\')
		{
			$this->options['savepath'] .= '/';
		}
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
			$this->filename = $this->options['savepath'] . $this->options['fileprefix'] . $this->sid;
		}

		if(file_exists($this->filename))
		{
			unlink($this->filename);
		}

		$this->sid = hash('sha1', $this->filename . $this->options['randseed']);
		$this->filename = $this->options['savepath'] . $this->options['fileprefix'] . $this->sid;

		if($clearData)
		{
			$this->data = array();
		}

		return $this->sid;
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
		$this->filename = $this->options['savepath'] . $this->options['fileprefix'] . $this->sid;
//echo $this->filename;
		if(file_exists($this->filename))
		{
			$this->data = unserialize(file_get_contents($this->filename));
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

		file_put_contents($this->options['savepath'] . $this->options['fileprefix'] . $this->sid, serialize($this->data));
	}

	/*
	 * Garbage collection
	 * Should be called periodically
	 */
	public function gc()
	{
		$files = scandir($this->options['savepath']);
		$cutoff = time() - $this->options['gctime'];

		foreach($files as $file)
		{
			$fullpath = $this->options['savepath'] . $file;

			if (substr($file, 0, strlen($this->options['fileprefix'])) == $this->options['fileprefix'] &&
				filemtime($fullpath) < $cutoff)
			{
				unlink($fullpath);
			}
		}
	}
}
