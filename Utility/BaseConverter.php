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

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Base Convertor
 * 		OOP interface for converting between different bases of arbitrary charsets.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 */
class BaseConverter
{
	/*
	 * @var string - convert to charset (defaults to hex)
	 */
	private $charsetTo = array();

	/*
	 * @var string - convert from charset (defaults to dec)
	 */
	private $charsetFrom = array();

	/*
	 * Get Instance
	 *
	 * @return instance of this class
	 */
	public static function getInstance()
	{
		return new static();
	}

	/*
	 * Set Charset (coverting to)
	 *
	 * @var string charset - String of characters for converting to
	 * @return $this
	 */
	public function setCharsetTo($charset)
	{
		$this->charsetTo = (string) $charset;
		return $this;
	}

	/*
	 * Set Charset (coverting from)
	 *
	 * @var string charset - String of characters for converting from
	 * @return $this	 
	 */
	public function setCharsetFrom($charset)
	{
		$this->charsetFrom = (string) $charset;
		return $this;
	}

	/*
	 * Convert
	 *
	 * @var string convert - The string to convert
	 * @return string - The output string 
	 */
	public function convert($convert)
	{
		return $output;
	}
}
