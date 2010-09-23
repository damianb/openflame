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

if(!defined('IN_OF_TEST')) exit;

// report ALL errors, ALWAYS.
@error_reporting(-1);
@ini_set('display_errors', 1);

// start loading stuff
require OF_ROOT . 'Of.php';
require OF_ROOT . 'OfException.php';
require OF_ROOT . 'OfCLI.php';
require OF_ROOT . 'OfCLIHandler.php';
require OF_TEST_ROOT . 'OfTestBase.php';

// We need the UI instantiated here before continuing.
$ui = new OfCLI();
Of::storeObject('ui', $ui);

// Disable color support if we need to.
if(defined('OFCLI_DISABLE_COLORS'))
	$ui->enable_colors = false;

// Set error and exception handlers.
set_error_handler('OfCLIHandler::catchError');
set_exception_handler('OfCLIHandler::catchException');
