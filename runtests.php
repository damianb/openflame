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
define('OF_TEST_ROOT', dirname(__FILE__) . '/tests/');
define('IN_OF_TEST', true);
//define('OFCLI_DISABLE_COLORS', true);

/**
 * @note Edit this to have a test ignored
 */
$ignore_tests = array(
	'OfTestCache.php',
	'OfTestLanguage.php',
	'OfTestOf.php',
	'OfTestTwig.php',
);

require OF_TEST_ROOT . 'Bootstrap.php';

if(!file_exists(OF_TEST_ROOT . 'tests/') || !is_dir(OF_TEST_ROOT . 'tests/'))
	throw new Exception('OpenFlame Framework Tests directory is not available');

$tests = array();

$dir_contents = @scandir(OF_TEST_ROOT . 'tests/');
foreach($dir_contents as $file)
{
	if($file[0] == '.' || substr(strrchr($file, '.'), 1) != 'php' || !is_file(OF_TEST_ROOT . 'tests/' . $file) || in_array($file, $ignore_tests))
		continue;
	$tests[] = $file;
}

if(empty($tests))
{
	$ui->output('NOTICE: No tests found, terminating script.', 'INFO');
	exit;
}

$i = 0;
foreach($tests as $test)
{
	if(($include = @include(OF_TEST_ROOT . 'tests/' . $test)) === false)
		throw new Exception(sprintf('Unable to include test file "%1$s"', $test));

	$test_class = substr($test, 0, strrpos($test, '.'));

	if(!class_exists($test_class, false))
		throw new Exception(sprintf('Test class "%1$s" missing'));

	/* @var OfTestBase */
	$obj = new $test_class();
	$obj->prepareTests();
	if(!$obj->runTests())
		$i++;
}

if($i > 0)
{
	$ui->output('', 'WARNING');
	$ui->output('WARNING: ' . $i . ' test files failed', 'WARNING');
	$ui->output('', 'WARNING');
}
else
{
	$ui->output('NOTICE: All tests passed', 'INFO');
}

$ui->output('STATUS: All tests completed, terminating', 'STATUS');
exit;
