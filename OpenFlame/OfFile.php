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

// Make sure we have our class here
if(!class_exists('OfInput') && file_exists(ROOT_PATH . 'OfInput.php'))
	require ROOT_PATH . 'OfInput.php';
else if(!file_exists(ROOT_PATH . 'OfInput.php') && !class_exists('OfInput'))
	exit;

/**
 * OpenFlame Web Framework - File Input
 * 	     Extends the Input class to handle file uploads.
 *
 *
 * @author      David King ("imkingdavid")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 */
class OfFile extends OfInput
{
	/**
	* Constructor 
	*
	* @param string $source The source of the file. Should be either "form" or "url"
	* @param string $file If $source if "url", the URL of the file. If $source if "form", the file form field name e.g. <input type="file" name="the_file" /> would make $file = 'the_file';
	* @param string $destionationDir The directory for the file to be uploaded to
	*/
	function __construct($source = 'form', $file, $destinationDir)
	{
		switch($source)
		{
			default:
			case 'form':
				parent::__construct($file, array('' => ''), '_FILES');
				// if there was an error with the upload
				if($this->cleanedInput['error'] != UPLOAD_ERR_OK)
				{
					throw new OfFileException($this->cleanedInput['error'], OfFileException::ERR_FILE_UPLOAD_ERROR);
				}
				
				if($this->verify($destinationDir))
				{
					move_uploaded_file($this->cleanedInput['tmp_name'], $destinationDir . $this->rawInput['name']);
				}
			break;
			
			case 'url';
				// first we validate the URL before even pulling its contents
				parent::__construct($file, '', '_POST');
				if(!$this->validate('url'))
				{
					throw new OfFileException('Invalid URL provided', OfFileException::ERR_FILE_URL_INVALID);
				}
				
				// @TODO: pull the url's content and validate it
			break;
		}
	}

	/**
	* verify() 
	*
	*/
	// @TODO: get a proper default max filesize; 300000 was just an example on php.net
	function verify($path = './', $max_filesize = 300000)
	{
		if(!sizeof($this->cleanedInput))
			throw new OfFileException('File information array empty', OfFileException::ERR_FILE_INFO_MISSING);
		
		// get array of disallowed extensions; for now, hardcoded
		$disallowed_ext = array('exe', 'zip', 'rar', '7z', 'gzip');
		if(in_array(end(explode(".", $this->cleanedInput['name'])), $disallowed_ext))
			throw new OfFileException('File extension not allowed', OfFileException::ERR_FILE_EXT_NOT_ALLOWED);
			
		// check the filesize
		if($max_filesize < $this->cleanedInput['size'])
			throw new OfFileException('File is too large', OfFileException::ERR_FILE_TOO_BIG);
		else if($this->cleanedInput['size'] == 0)
			throw new OfFileException('File is zero bytes', OfFileException::ERR_FILE_ZERO_BYTES);
		
		return true;
	}
}