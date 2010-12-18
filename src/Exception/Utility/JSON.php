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

namespace OpenFlame\Framework\Exception\Utility;

if(!defined('OpenFlame\\Framework\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - JSON Exception class,
 * 		Used for exceptions generated in the JSON utility class.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note this class reserves the 4xxx exception code range
 */
class JSON extends \OpenFlame\Framework\Exception\Base
{
	const ERR_JSON_UNKNOWN = 4100;
	const ERR_JSON_NO_ERROR = 4101;
	const ERR_JSON_DEPTH = 4102;
	const ERR_JSON_CTRL_CHAR = 4103;
	const ERR_JSON_SYNTAX = 4104;
}
