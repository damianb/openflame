<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  utility
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Core\Utility;
use \OpenFlame\Framework\Core\Internal\RuntimeException;

/**
 * OpenFlame Framework - JSON handling class,
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
	 *
	 * @throws RuntimeException
	 */
	public static function encode($data)
	{
		if(empty($data))
		{
			return NULL;
		}

		$json = json_encode($data);

		if($json === NULL)
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

			throw new RuntimeException($error);
		}

		return $json;
	}

	/**
	 * Loads a JSON string or file and returns the data held within.
	 * @param string $json - The JSON string or the path of the JSON file to decode.
	 * @return array - The contents of the JSON string/file.
	 *
	 * @throws RuntimeException
	 */
	public static function decode($json)
	{
		if(is_file($json))
		{
			$json = file_get_contents($json);
		}

		// Empty JSON data?  o_O
		if(empty($json))
		{
			return NULL;
		}

		$json = preg_replace("/^[\t ]*#[^\n]*\n?/m", '', $json);
		$data = json_decode($json, true);

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

			throw new RuntimeException($error);
		}

		return $data;
	}
}
