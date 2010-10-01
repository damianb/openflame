<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - JSON handling class,
 * 		OOP interface for use with JSON files/strings.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note        This class should not be instantiated; it should only be statically accessed.
 */
class OfJSON
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
	 * @param boolean $is_file - Are we loading from a JSON file?
	 * @return array - The contents of the JSON string/file.
	 *
	 * @throws OfJSONException
	 */
	public static function decode($json, $is_file = true)
	{
		if($is_file)
		{
			if(!file_exists($json))
				throw new OfJSONException('JSON file does not exist', OfJSONException::ERR_JSON_NO_FILE);
			$json = file_get_contents($json);
		}

		$data = json_decode(preg_replace('#\#.*?' . PHP_EOL . '#', '', $json), true);

		if($data === NULL)
		{
			if(function_exists('json_last_error'))
			{
				switch(json_last_error())
				{
					case JSON_ERROR_NONE:
						$error = 'No error';
						$code = OfJSONException::ERR_JSON_NO_ERROR;
					break;

					case JSON_ERROR_DEPTH:
						$error = 'Maximum JSON recursion limit reached.';
						$code = OfJSONException::ERR_JSON_DEPTH;
					break;

					case JSON_ERROR_CTRL_CHAR:
						$error = 'Control character error';
						$code = OfJSONException::ERR_JSON_CTRL_CHAR;
					break;

					case JSON_ERROR_SYNTAX:
						$error = 'JSON syntax error';
						$code = OfJSONException::ERR_JSON_SYNTAX;
					break;

					default:
						$error = 'Unknown JSON error';
						$code = OfJSONException::ERR_JSON_UNKNOWN;
					break;
				}
			}
			else
			{
				// Since we don't have json_last_error(), which is PHP 5.3+, we just say it is OfJSONException::ERR_JSON_UNKNOWN, and move on.
				$error = 'Unknown JSON error';
				$code = OfJSONException::ERR_JSON_UNKNOWN;
			}

			throw new OfJSONException($error, $code);
		}

		return $data;
	}
}
