<?php
/**
 *
 * @package OpenFlame Web Framework
 * @copyright (c) 2010 OpenFlameCMS.com
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - Primary Exception class,
 * 		Extension of the default Exception class.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 */
class OfException extends Exception
{
	// wat
	const ERR_WTF = 0;
}

/**
 * OpenFlame Web Framework - JSON Exception class,
 * 		Used for exceptions generated in the JSON library.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 */
class OfJSONException extends OfException
{
	const ERR_JSON_NO_FILE = 4000;
	const ERR_JSON_UNKNOWN = 4100;
	const ERR_JSON_NO_ERROR = 4101;
	const ERR_JSON_DEPTH = 4102;
	const ERR_JSON_CTRL_CHAR = 4103;
	const ERR_JSON_SYNTAX = 4104;
}
