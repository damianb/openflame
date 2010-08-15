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
	* @param string $file The file form field name e.g. <input type="file" name="the_file" /> would make $file = 'the_file';
	* @param string $destionation_dir The directory for the file to be uploaded to
	*/
	function __construct($file, $destinationDir)
	{
		parent::__construct($file, array('' => ''), '_FILES');
		
		move_uploaded_file($this->rawInput['tmp_name'], $destinationDir . $this->rawInput['name']);
	}
	
	/**
	* @TODO: Alright, so currently this takes a form-uploaded file and moves it to a destination directory.
	*		I still need to verify file type and size and such.
	*/	
}
