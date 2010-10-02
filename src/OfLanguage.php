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

if(!defined('ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Internationalization class,
 * 		Provides an easy-to-use interface for working with the language files.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfLanguage
{
	public $lang_paths = array();

	public function __construct($lang_paths)
	{
		$this->lang_paths = (!is_array($lang_paths)) ? array($lang_paths) : $lang_paths;
	}

	public function loadFile($filename)
	{
		// file_exists checks and a bunch of other stuff
	}

	public function getLangVar($lang_key)
	{
		// asdf
	}

	public function getJSLangVar($lang_key)
	{
		// asdf
		// need to use addslashes or whatever for use within javascript, if we need it
	}

	public function deployLang($lang_prefix, $js_lang_prefix)
	{
		// asdf
	}
}
