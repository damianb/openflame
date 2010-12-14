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

if(!defined('OF_ROOT')) exit;

/**
 * OpenFlame Web Framework - Primary Exception class,
 * 		Extension of the default Exception class.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfException extends Exception
{
	const ERR_WTF = 0;  // wat
}

/**
 * OpenFlame Web Framework - Cache Exception class,
 * 		Used for exceptions generated in the Cache library.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note this class reserves the 2xxx exception code range
 */
class OfCacheException extends OfException
{
	const ERR_CACHE_UNWRITABLE = 2000;
	const ERR_CACHE_UNREADABLE = 2001;
	const ERR_CACHE_FOPEN_FAILED = 2002;
	const ERR_CACHE_FWRITE_FAILED = 2003;
	const ERR_CACHE_FLOCK_FAILED = 2004;

	const ERR_CACHE_ENGINE_NOT_CACHEBASE_CHILD = 2100;
	const ERR_CACHE_ENGINE_NOT_CACHEINTERFACE_CHILD = 2101;

	const ERR_CACHE_PATH_NO_ACCESS = 2200;
}

/**
 * OpenFlame Web Framework - File Exception class,
 * 		Used for exceptions generated in the File library.
 *
 *
 * @author      David King ("imkingdavid")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note this class reserves the 3xxx exception code range
 */
class OfFileException extends OfException
{
	const ERR_FILE_TOO_BIG = 3000;
	const ERR_FILE_ZERO_BYTES = 3001;
	const ERR_FILE_EXT_NOT_ALLOWED = 3002;
	const ERR_FILE_UPLOAD_ERROR = 3003;
	const ERR_FILE_URL_INVALID = 3004;
	const ERR_FILE_INFO_MISSING = 3005;
}

/**
 * OpenFlame Web Framework - JSON Exception class,
 * 		Used for exceptions generated in the JSON library.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note this class reserves the 4xxx exception code range
 */
class OfJSONException extends OfException
{
	const ERR_JSON_UNKNOWN = 4100;
	const ERR_JSON_NO_ERROR = 4101;
	const ERR_JSON_DEPTH = 4102;
	const ERR_JSON_CTRL_CHAR = 4103;
	const ERR_JSON_SYNTAX = 4104;
}

/**
 * OpenFlame Web Framework - CLIArgs Exception class,
 * 		Used for exceptions generated in the CLI Args input library.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note this class reserves the 5xxx exception code range
 */
class OfCLIArgsException extends OfException
{
	// asdf
}
