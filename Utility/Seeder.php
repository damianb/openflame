<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  security
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Utility;
use \OpenFlame\Framework\Utility\Internal\SeederException;

/**
 * OpenFlame Framework - Random string/seed generator
 * 	     Provides a coherent random seed or string for use in applications.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Seeder
{
	/**
	 * @var string - The session-based seed to use when generating random strings/seeds.
	 */
	protected $session_seed = '';

	/**
	 * @var string - The application-based seed to use when generating random strings/seeds.
	 */
	protected $application_seed = '';

	/**
	 * Get the current session seed string
	 * @return string - The current session seed string.
	 */
	public function getSessionSeed()
	{
		return $this->session_seed;
	}

	/**
	 * Set the session seed string to use for generating random strings/seeds
	 * @param string $seed - The session-specific seed to use for random string/seed generation.
	 * @return \OpenFlame\Framework\Security\Seeder - Provides a fluent interface.
	 */
	public function setSessionSeed($seed)
	{
		$this->session_seed = $seed;

		return $this;
	}

	/**
	 * Get the current application seed string
	 * @return string - The current application seed string.
	 */
	public function getApplicationSeed()
	{
		return $this->application_seed;
	}

	/**
	 * Set the application seed string to use for generating random strings/seeds
	 * @param string $seed - The application-specific seed to use for random string/seed generation.
	 * @return \OpenFlame\Framework\Security\Seeder - Provides a fluent interface.
	 */
	public function setApplicationSeed($seed)
	{
		$this->application_seed = $seed;

		return $this;
	}

	/**
	 * Create a random string of a specified length
	 * @param integer $length - The length of the string to return.
	 * @param string $seed - Additional randomness for generating the random string with.
	 * @param string $charset - The range of characters to use for the random string (will not modify character cases!)
	 * @return string - A random string!
	 *
	 * @throws SeederException
	 */
	public function buildRandomString($length = 12, $seed = '', $charset = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
	{
		if($length > 64)
		{
			throw new SeederException('Length specified for random string exceeds maximum allowed length of 64 characters');
		}
		elseif($length < 1)
		{
			return '';
		}

		$converter = BaseConverter::newInstance()
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
			$return .= substr($string, 0, $length - strlen($return));
		}
		return $return;
	}

	/**
	 * @ignore
	 */
	public function buildSeedString($algo = 'md5', $pad_length = 46, array $extra = array())
	{
		$hash = hash($algo, implode('', array_merge(array($this->getSessionSeed(), $this->getApplicationSeed()), $extra)));
		return str_pad(implode('', array_map('hexdec', str_split($hash, 2))), (int) $pad_length, '0', STR_PAD_LEFT);
	}
}
