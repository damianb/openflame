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
	 * @var string Relative path to the file (including the filename)
	 */
	public $fileName;
	/**
	 * @var string md5 Hash of the file for verifyinng the integrity (possibly store in the database for later?)
	 */
	public $fileMd5Hash;
	/**
	 * @var int Size of the file
	 */
	public $fileSize;

	/**
	* Constructor method called when the object is instantiated.
	* Handles the file upload and verification so that no further methods must be called.
	*
	* @param string $source The source of the file. Should be either "upload" or "url"
	* @param string $file The name of the form field.
	* @param string $destionationDir The directory for the file to be uploaded to
	*
	* @throws OfFileException
	*/
	public function __construct($source = 'upload', $file, $destinationDir)
	{
		switch($source)
		{
			default:
			case 'upload':
				parent::__construct($file, array('' => ''), '_FILES');
				// if there was an error with the upload
				if($this->cleanedInput['error'] != UPLOAD_ERR_OK)
					throw new OfFileException($this->cleanedInput['error'], OfFileException::ERR_FILE_UPLOAD_ERROR);
				
				if($this->verify())
					move_uploaded_file($this->cleanedInput['tmp_name'], $destinationDir . $this->cleanedInput['name']);
				
				$this->fileName = $destinationDir . $this->cleanedInput['name'];
				$this->fileMd5Hash = hash_file('md5', $this->fileName);
				$this->fileSize = $this->cleanedInput['size'];
			break;
			
			case 'url';
				// first we validate the URL before even pulling its contents
				parent::__construct($file, '', '_POST');
				if(!$this->validate('url'))
					throw new OfFileException('Invalid URL provided', OfFileException::ERR_FILE_URL_INVALID);
				
				// @TODO: pull the url's content and validate it
			break;
		}
	}

	/**
	* Verifies the uploaded file to make sure it is safe to use and all restrictions are met. 
	*
	* @param int $maxFilesize The maximum size allowed for an uploaded file.
	* @return boolean - Returns true if successful
	*
	* @throws OfFileException
	*/
	// @TODO: get a proper default max filesize; 300000 was just an example on php.net
	public function verify($maxFilesize = 300000)
	{
		if(empty($this->cleanedInput)
			throw new OfFileException('File information array empty', OfFileException::ERR_FILE_INFO_MISSING);
		
		// get array of disallowed extensions; for now, hardcoded
		$disallowed_ext = array('exe', 'zip', 'rar', '7z', 'gzip');
		if(in_array(end(explode(".", $this->fileName)), $disallowed_ext))
			throw new OfFileException('File extension not allowed', OfFileException::ERR_FILE_EXT_NOT_ALLOWED);
			
		// check the filesize
		if($maxFilesize < $this->fileSize)
			throw new OfFileException('File is too large', OfFileException::ERR_FILE_TOO_BIG);
		else if($this->fileSize == 0)
			throw new OfFileException('File is zero bytes', OfFileException::ERR_FILE_ZERO_BYTES);
		
		return true;
	}
}