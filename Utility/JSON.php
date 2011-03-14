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
 * OpenFlame Web Framework - JSON handling class,
 * 		OOP interface for use with JSON files/strings.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note        This class should not be instantiated; it should only be statically accessed.
 */
abstract class JSON
{
	/**
	 * Builds a JSON string based on input.
	 * @param mixed $data - The data to cache.
	 * @return string - JSON string.
	 */
	public static function encode($data)
	{
		return json_encode($data);
	}

	/**
	 * Loads a JSON string or file and returns the data held within.
	 * @param string $json - The JSON string or the path of the JSON file to decode.
	 * @return array - The contents of the JSON string/file.
	 *
	 * @throws \RuntimeException
	 */
	public static function decode($json)
	{
		if(is_file($json))
			$json = file_get_contents($json);

		$data = json_decode(preg_replace('#\#.*?' . "\n" . '#', '', $json), true);

		if($data === NULL)
		{
			switch(json_last_error())
			{
				case JSON_ERROR_NONE:
					$error = 'No error';
				break;

				case JSON_ERROR_DEPTH:
					$error = 'Maximum JSON recursion limit reached.';
				break;

				case JSON_ERROR_CTRL_CHAR:
					$error = 'Control character error';
				break;

				case JSON_ERROR_SYNTAX:
					$error = 'JSON syntax error';
				break;

				default:
					$error = 'Unknown JSON error';
				break;
			}

			throw new \RuntimeException(sprintf('JSON error:"%1$s"', $error));
		}

		return $data;
	}
}
