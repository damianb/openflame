<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.2.3
 */

define('OF_ROOT', dirname(__FILE__) . '/src/');
require OF_ROOT . 'Of.php';
require OF_ROOT . 'OfException.php';

// Register the autoloader
spl_autoload_register('Of::loader');
// Register exception handler
set_exception_handler('OfHandler::catcher');

// blah blah blah
// add to this later
