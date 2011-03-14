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
 * OpenFlame Web Framework - Base Convertor
 * 		OOP interface for converting between different bases of arbitrary charsets.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * @todo		Refactor class to remove the base 10 proxy of conversion 
 */
class BaseConverter
{
	/*
	 * @var string - convert to charset (defaults to hex)
	 */
	protected $charsetTo = array();

	/*
	 * @var string - convert from charset (defaults to dec)
	 */
	protected $charsetFrom = array();

	/*
	 * Build-in bases 
	 */
	const BASE_2 	= '01';
	const BASE_10	= '0123456789';
	const BASE_16	= '0123456789ABCDEF';

	/*
	 * Get Instance
	 *
	 * @return \OpenFlame\Framework\Utility\BaseConverter - Provides a fluent interface.
	 */
	public static function getInstance()
	{
		return new static();
	}

	/*
	 * Set Charset (coverting to)
	 *
	 * @var mixed charset - String of characters for converting to
	 * @return $this
	 */
	public function setCharsetTo($charset)
	{
		$this->charsetTo = is_array($charset) ? $charset : str_split((string) $charset);
		return $this;
	}

	/*
	 * Set Charset (coverting from)
	 *
	 * @var mixed charset - String of characters for converting from
	 * @return $this	 
	 */
	public function setCharsetFrom($charset)
	{
		$this->charsetFrom = is_array($charset) ? $charset : str_split((string) $charset);
		return $this;
	}

	/*
	 * Decode to base 10
	 *
	 * @param string - String to be decoded, must be within the charset of 
	 *	charsetTo.
	 * @return string - Base 10 representation of the number 
	 */
	public function decode($input)
	{
		$_charsetFrom = array_flip($this->charsetFrom);
		$input = str_split(strrev($input));
		$base = (string) sizeof($_charsetFrom);

		// No support for floating point integers for the base 10 proxy 
		bcscale(0);

		$inputSize = sizeof($input);
		$output = '';
		for($i = 0; $inputSize > $i; $i++)
		{
			// Throw an exception if we should encounter an illegal key
			if(!array_key_exists($input[$i], $_charsetFrom))
			{
				throw new \OutOfRangeException("Input of base conversion is out of range from the specified charset");
			}

			$output = bcadd($output, bcmul($_charsetFrom[$input[$i]], bcpow($base, $i, 0)));
		}

		return $output;
	}

	/*
	 * Encode to base from base 10
	 *
	 * @param string - base 10 integer
	 * @return string base in the charset of 
	 */
	public function encode($input)
	{
		$output = '';
		$base = (string) sizeof($this->charsetTo);

		// No support for floating point integers for the base 10 proxy 
		bcscale(0);

		do
		{
			$rem	= bcmod($input, $base);
			$input	= bcdiv($input, $base);

			$output = $this->charsetTo[(int) $rem] . $output;
		}
		while(bccomp($input, '1') != -1);

		return $output;
	}

	/*
	 * Convert
	 *
	 * @var string convert - The string to convert
	 * @return string - The output string 
	 */
	public function convert($convert = '')
	{
		// All your base are belong to us.
		return empty($convert) ? '' : $this->encode($this->decode($convert));
	}
}
