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

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

// Make sure we have our class here
// @todo replace with a proper dependency injection
if(!class_exists('OfInput') && file_exists(ROOT_PATH . 'OfInput.php'))
	require ROOT_PATH . 'OfInput.php';
elseif(!file_exists(ROOT_PATH . 'OfInput.php') && !class_exists('OfInput'))
	exit;

/**
 * OpenFlame Web Framework - File Input
 * 	     Extends the Input class to handle file uploads.
 *
 *
 * @author      David King ("imkingdavid")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfFile extends OfInput
{

	/**
	 * @var string Relative path to the file (including the filename)
	 */
	public $file_name;
	/**
	 * @var string md5 Hash of the file for verifyinng the integrity (possibly store in the database for later?)
	 */
	public $file_md5_hash;
	/**
	 * @var int Size of the file
	 */
	public $file_size;

	/**
	 * Constructor, handles the file upload and verification so that no further methods must be called.
	 * @param string $source The source of the file. Should be either "upload" or "url"
	 * @param string $file The name of the form field.
	 * @param string $destionation_dir The directory for the file to be uploaded to
	 * @throws OfFileException
	 */
	public function __construct($source = 'upload', $file, $destination_dir)
	{
		switch($source)
		{
			default:
			case 'upload':
				parent::__construct($file, array('' => ''), '_FILES');
				// if there was an error with the upload
				if($this->cleaned_input['error'] != UPLOAD_ERR_OK)
					throw new OfFileException($this->cleaned_input['error'], OfFileException::ERR_FILE_UPLOAD_ERROR);

				if($this->verify())
					move_uploaded_file($this->cleaned_input['tmp_name'], $destination_dir . $this->cleaned_input['name']);

				$this->file_name = $destination_dir . $this->cleaned_input['name'];
				$this->file_md5_hash = hash_file('md5', $this->file_name);
				$this->file_size = $this->cleaned_input['size'];
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
	 * @param int $max_filesize The maximum size allowed for an uploaded file.
	 * @return boolean - Returns true if successful
	 *
	 * @throws OfFileException
	 */
	// @TODO: get a proper default max filesize; 300000 was just an example on php.net
	public function verify($max_filesize = 300000)
	{
		if(empty($this->cleaned_input))
			throw new OfFileException('File information array empty', OfFileException::ERR_FILE_INFO_MISSING);

		// get array of disallowed extensions; for now, hardcoded
		$disallowed_ext = array('exe', 'zip', 'rar', '7z', 'gzip');
		if(in_array(end(explode(".", $this->file_name)), $disallowed_ext))
			throw new OfFileException('File extension not allowed', OfFileException::ERR_FILE_EXT_NOT_ALLOWED);

		// check the filesize
		if($max_filesize < $this->file_size)
		{
			throw new OfFileException('File is too large', OfFileException::ERR_FILE_TOO_BIG);
		}
		else if($this->file_size == 0)
		{
			throw new OfFileException('File is zero bytes', OfFileException::ERR_FILE_ZERO_BYTES);
		}

		return true;
	}
}
