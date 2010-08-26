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

if(!defined('IN_OF_TEST')) exit;

/**
 * OpenFlame Web Framework - CLI interface class,
 * 	    Provides the rough shell for interaction via CLI.
 *
 *
 * @category    OpenFlame Framework
 * @package     tests
 * @author      Damian Bushong ("Obsidian")
 * @license     MIT License
 */
class OfTestBase implements OfTestInterface
{
	/**
	 * @var array - Array of callbacks to methods in this object that will be run for testing.
	 */
	protected $test_ary = array();

	/**
	 * Automatically runs all tests that are specified in $this->test_ary
	 * @return boolean - Were all tests successful, or did some fail?
	 */
	final public function runTests()
	{
		$ui->output('STATUS: Running test suite ' . get_class($this), 'INFO');
		$i = 0;
		foreach($this->test_ary as $test)
		{
			$test = "test$test";
			if(!$this->$test())
				$i++;
		}
		if($i > 0)
		{
			$ui->output('', 'WARNING');
			$ui->output('WARNING: ' . $i . ' tests failed in test module ' . get_class($this), 'WARNING');
			$ui->output('', 'WARNING');
			return false;
		}
		else
		{
			$ui->output('NOTICE: All tests passed in test module' . get_class($this), 'INFO');
			return true;
		}
	}

	/**
	 * A simple test function.  Makes sure that everything matches up as expected.
	 * @param string $test_name - The name or descripton of the test, so we can figure out what we are testing when the test is run (in case it fails)
	 * @param mixed $test_result - The results of the test
	 * @param mixed $expect - What do we expect the test to produce?
	 * @return boolean - Was the test successful?
	 */
	final protected function expect($test_name, $test_result, $expect)
	{
		global $ui;

		$ui->output('NOTICE: Running test: ' . $test_name, 'INFO');
		if($test_result !== $expect)
		{
			$ui->output('', 'ERROR');
			$ui->output('ERROR: Test failed!', 'ERROR');
			$ui->output('ERROR: Expected:  ' . print_r($expect, true), 'ERROR');
			$ui->output('ERROR: Got:       ' . print_r($test_result, true), 'ERROR');
			$ui->output('', 'ERROR');
			return false;
		}
		else
		{
			$ui->output('NOTICE: Test passed!', 'INFO');
			return true;
		}
	}
}

/**
 * OpenFlame Web Framework - CLI interface class,
 * 		Provides the rough shell for interaction via CLI.
 *
 *
 * @category    OpenFlame Framework
 * @package     tests
 * @author      Damian Bushong ("Obsidian")
 * @license     MIT License
 */
interface OfTestInterface
{
	public function prepareTests();
	public function runTests();
	public function expect($test_name, $test_result, $expect);
}
