<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.2.3
 *
 * @uses Of
 * @uses OfCLI
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - CLI error handler class,
 * 		Provides an error/exception handler.
 *
 *
 * @author      Damian Bushong ("Obsidian")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * @note        This class should not be instantiated; it should only be statically accessed.
 */
class OfCLIHandler
{
	/**
	 * @var Exception - The exception to store
	 */
	public static $exception;

	/**
	 * Catches an exception and prepares to deal with it
	 * @param Exception $e - The exception to handle
	 * @return void
	 */
	public static function catchException($e)
	{
		/* @var OfCLI */
		$ui = Of::obj('ui');

		self::$exception = $e;

		$e = array(
			'e_type' => get_class(self::$exception),
			'message' => self::$exception->getMessage(),
			'code' => self::$exception->getCode(),
			'trace' => implode(self::traceException(self::$exception->getFile(), self::$exception->getLine(), 6)),
			'file' => self::$exception->getFile(),
			'line' => self::$exception->getLine(),
			'stack' => implode(self::formatStackTrace()),
		);

		if(!$e['stack'])
			$e['stack'] = 'No stack trace available.';

		$error = <<<EOD
Exception thrown; exception {$e['e_type']}::{$e['code']} with message "{$e['message']}"
on line {$e['line']} in file: {$e['file']}

Trace context:
{$e['trace']}

Stack trace
{$e['stack']}
EOD;
		$ui->output($error, 'ERROR');
	}

	/**
	 * Error handling method
	 */
	public static function catchError($errno, $errstr, $errfile, $errline, $errcontext)
	{
		/* @var OfCLI */
		$ui = Of::obj('ui');

		switch($errno)
		{
			case E_NOTICE:
			case E_USER_NOTICE:
				$error = <<<EOD
Notice encountered; message "{$errstr}"
on line {$errline} in file: {$errfile}
EOD;
				$type = 'info';
			break;

			case E_USER_STRICT:
			case E_STRICT:
				$error = <<<EOD
Strict notice encountered; message "{$errstr}"
on line {$errline} in file: {$errfile}
EOD;
				$type = 'info';
			break;

			case E_DEPRECATED:
			case E_USER_DEPRECATED:
$error = <<<EOD
Deprecation notice encountered; message "{$errstr}"
on line {$errline} in file: {$errfile}
EOD;
				$type = 'info';
			break;

			case E_WARNING:
			case E_USER_WARNING:
				$error = <<<EOD
Warning encountered; message "{$errstr}"
on line {$errline} in file: {$errfile}
EOD;
				$type = 'warning';
			break;

			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				$error = <<<EOD
Fatal error encountered; message "{$errstr}"
on line {$errline} in file: {$errfile}
EOD;
				$type = 'error';
			break;
		}


		$ui->output($error, $type);
		if($type === 'error')
			die();
	}

	/**
	 * Retrieves the context code from where an exception was thrown (as long as file/line are provided) and outputs it.
	 * @param string $file - The file where the exception occurred.
	 * @param string $line - The line where the exception occurred.
	 * @param integer $context - How many lines of context (above AND below) the troublemaker should we grab?
	 * @return string - String containing the perpetrator + context lines for where the error/exception was thrown.
	 */
	public static function traceException($file, $line, $context = 3)
	{
		$return = array();
		foreach (file($file) as $i => $str)
		{
			if (($i + 1) > ($line - $context))
			{
				if(($i + 1) > ($line + $context))
					break;
				$return[] = $str;
			}
		}

		return $return;
	}

	/**
	 * Format the stack trace for the currently loaded exception
	 * @return string - The string containing the formatted stack trace
	 */
	public static function formatStackTrace()
	{
		$return = array();
		$stack = self::$exception->getTrace();

		if(!$stack)
			return array();

		foreach($stack as $id => $trace)
		{
			$callback = (isset($trace['class']) ? $trace['class'] . $trace['type'] : '') . $trace['function'] . '(' . (!empty($trace['args']) ? implode(',', $trace['args']) : '') . ')';
			$return[] = <<<EOD
Trace #{$id}
    file: {$trace['file']}
    line: {$trace['line']}
    callback: {$callback}
EOD;
		}
		return $return;
	}
