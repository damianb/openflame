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

	public function buildRandomString($length = 12, $seed = '', $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
		if($length > 64)
		{
			throw new \InvalidArgumentException('Length specified for random string exceeds maximum allowed length of 64 characters');
		}
		elseif($length < 1)
		{
			return '';
		}

		$converter = \OpenFlame\Framework\Utility\BaseConverter::newInstance()
			->setCharsetTo($charset);

		$seed_string = $this->buildSeedString('sha256', 88, array(mt_rand(), $seed));
		$string = $converter->encode($seed_string);

		// Random offset here, we'll loop over to the start of the string if we don't have enough characters at the start of the string.
		$start = mt_rand(0, strlen($string));
		if($start == 0)
		{
			return substr($string, 0, $length);
		}

		$return = substr($string, $start, $length);
		if(strlen($return) < $length)
		{
			$return .= substr($return, 0, strlen($return) - $length);
		}
		return $return;
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
