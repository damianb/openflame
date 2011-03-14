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

namespace OpenFlame\Framework\Security;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Random string/seed generator
 * 	     Provides a coherent random seed or string for use in applications.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Seeder
{
	protected $last_seed = '';

	protected $last_seed_time = 0;

	protected $session_seed = '';

	protected $application_seed = '';

	protected $instance_seed_count = 0;

	public function getSessionSeed()
	{
		return $this->session_seed;
	}

	public function setSessionSeed($seed)
	{
		$this->session_seed = $seed;
		return $this;
	}

	public function getApplicationSeed()
	{
		return $this->application_seed;
	}

	public function setApplicationSeed($seed)
	{
		$this->application_seed = $seed;
		return $this;
	}

	public function buildRandomString($seed, $charset = NULL)
	{
		// asdf
	}

	public function buildSeedString($algo = 'md5', $pad_length = 46, array $extra = array())
	{
		$hash = hash($algo, implode('', array_merge(array($this->getSessionSeed(), $this->getApplicationSeed()), $extra)));
		$str = '';
		foreach(str_split($hash, 2) as $char)
		{
			$str .= hexdec($char);
		}
		return str_pad($str, (int) $pad_length, '0', STR_PAD_LEFT);
	}
}
