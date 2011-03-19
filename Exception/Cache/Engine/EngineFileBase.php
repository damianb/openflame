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

namespace OpenFlame\Framework\Exception\Cache\Engine;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - File-based cache engine Exception class,
 * 		Used for exceptions generated in the filecache engine base.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note this class reserves the 4xxx exception code range
 */
class EngineFileBase extends \OpenFlame\Framework\Exception\Base
{
	const ERR_CACHE_UNWRITABLE = 2000;
	const ERR_CACHE_UNREADABLE = 2001;
	const ERR_CACHE_FOPEN_FAILED = 2002;
	const ERR_CACHE_FWRITE_FAILED = 2003;
	const ERR_CACHE_FLOCK_FAILED = 2004;

	const ERR_CACHE_PATH_NOT_DIR = 2100;
	const ERR_CACHE_PATH_NO_ACCESS = 2101;
}
