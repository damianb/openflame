<?php
/**
 *
 * @package OpenFlame Web Framework
 * @version $Id$
 * @copyright (c) 2010 OpenFlameCMS.com
 * @license http://opensource.org/licenses/mit-license.php The MIT License
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

// Make sure we have our class here
if(!class_exsits('OfInput') && file_exists(ROOT_PATH . 'OfInput.php'))
	require ROOT_PATH . 'OfInput.php';
else if(!file_exists(ROOT_PATH . 'OfInput.php') && !class_exists('OfInput'))
	exit;

class OfFile extends OfInput
{
}
