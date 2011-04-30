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

namespace OpenFlame\Framework\Session\Autologin;
use \OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Sessions Autologin Engine
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class EngineFilesystem implements EngineInterface
{
	/*
	 * @var all options relating to thiis engine
	 */
	protected $options = array();

	/*
	 */
	protected $now = 0;

	/**
	 * Set options
	 * @param array - Key/value pair
	 */
	public function setOptions($options)
	{
		$this->options['autologin.savepath'] = isset($options['autologin.savepath']) ? 
			(string) $options['autologin.savepath'] : 
			(isset($options['file.savepath']) ? $options['file.savepath'] : ini_get('upload_tmp_dir'));

		$this->options['autologin.ttl'] = isset($options['autologin.ttl']) ? 
			(int) $options['autologin.ttl'] : 7776000; // Defaults to 90 days

		$this->options['autologin.prefix'] = isset($options['autologin.prefix']) ? 
			(string) $options['autologin.prefix'] : 'al_'; // Defaults to 90 days

		// I don't want your damn lemons
		if(substr($this->options['autologin.savepath'], -1) != '/' || substr($this->options['autologin.savepath'], -1) != '\\')
		{
			$this->options['autologin.savepath'] .= '/';
		}
	}

	/*
	 * Store a key/uid
	 * @param string key
	 * @param string uid
	 * @return bool 
	 */
	public function store($uid, $key)
	{
		if($this->now == 0)
		{
			$this->now = time();
		}

		return (file_put_contents($this->options['autologin.savepath'] . $this->options['autologin.prefix'] . $key . '.php', 
			"<?php exit; ?>\n{$this->now}\n{$uid}\n") > 0) ? true : false;
	}

	/*
	 * Lookup autologin by key and delete the old one
	 * @param string - key from the user 
	 * @return string - UID stored associated with the key or null
	 */
	public function lookup($key)
	{
		$file = $this->options['autologin.savepath'] . $this->options['autologin.prefix'] . $key . '.php';

		if(!file_exists($file))
		{
			return NULL;
		}

		if($this->now == 0)
		{
			$this->now = time();
		}

		// Offeset is the strlen of the php exit stuff
		$contents = file_get_contents($file, NULL, NULL, 15);
		list($ts, $uid) = explode("\n", $contents);
		unlink($file);

		$ts = (int) $ts;
		$uid = (string) $uid;

		if($ts == 0 || empty($uid))
		{
			return NULL;
		}

		return ($this->now < ($ts + $this->options['autologin.ttl'])) ? $uid : NULL;
	}

	/*
	 * Garbage collection
	 * Should be called periodically
	 */
	public function gc()
	{
		$files = scandir($this->options['autologin.savepath']);
		$cutoff = time() - $this->options['autologin.ttl'];

		foreach($files as $file)
		{
			$fullpath = $this->options['autologin.savepath'] . $file;

			if (substr($file, 0, strlen($this->options['autologin.prefix'])) == $this->options['autologin.prefix'])
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
