<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 *
 * @uses Of
 * @uses OfCLI
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

	public function prepareTests()
	{
		// pass
	}

	/**
	 * Automatically runs all tests that are specified in $this->test_ary
	 * @return mixed - If no tests failed, return false; otherwise, return the number of tests failed
	 */
	final public function runTests()
	{
		/* @var OfCLI */
		$ui = Of::obj('ui');
		$ui->output(sprintf(' Running test suite "%1$s"', get_class($this)), 'INFO');
		$i = 0;
		foreach($this->test_ary as $test)
		{
			$test = "test$test";
			$ui->output(sprintf('    Running test "%1$s::%2$s"', get_class($this), $test), 'INFO');
			$pre_time = microtime(true);
			if(!$this->$test())
			{
				$ui->output(sprintf('        Time taken: %1$s seconds',  microtime(true) - $pre_time), 'ERROR');
				$ui->output('', 'ERROR');
				$i++;
				if(!defined('OF_TEST_DISABLE_SLEEP'))
					sleep(2);
			}
			else
			{
				$ui->output(sprintf('        Time taken: %1$s seconds',  microtime(true) - $pre_time), 'INFO');
			}


		}
		if($i > 0)
		{
			$ui->output('', 'WARNING');
			$ui->output(sprintf(' %1$s test(s) failed in test module "%2$s"', $i, get_class($this)), 'WARNING');
			$ui->output('', 'WARNING');

			return $i;
		}
		else
		{
			$ui->output(sprintf('  All tests passed in test module "%1$s"', get_class($this)), 'INFO');
			return false;
		}
	}

	/**
	 * A simple test function.  Makes sure that everything matches up as expected.
	 * @param string $test_name - The name or descripton of the test, so we can figure out what we are testing when the test is run (in case it fails)
	 * @param mixed $test_result - The results of the test
	 * @param mixed $expect - What do we expect the test to produce?
	 * @return boolean - Was the test successful?
	 */
	final public function expect($test_name, $test_result, $expect)
	{
		/* @var OfCLI */
		$ui = Of::obj('ui');

		if($test_result !== $expect)
		{
			$ui->output('', 'ERROR');
			$ui->output(sprintf('     Test "%1$s" failed!', $test_name), 'ERROR');
			$ui->output(sprintf('        Expected:  %1$s(%2$s)', gettype($expect), $this->typeVariable($expect)), 'ERROR');
			$ui->output(sprintf('        Got:       %1$s(%2$s)', gettype($test_result), $this->typeVariable($test_result)), 'ERROR');
			return false;
		}
		else
		{
			$ui->output(sprintf('     Test "%1$s" passed', $test_name), 'INFO');
			$ui->output(sprintf('        Expected:  %1$s(%2$s)', gettype($expect), $this->typeVariable($expect)), 'INFO');
			$ui->output(sprintf('        Got:       %1$s(%2$s)', gettype($test_result), $this->typeVariable($test_result)), 'INFO');
			return true;
		}
	}

	/**
	 * Formats a variable to make it easy to understand what we're expecting/recieving.
	 * @param mixed $variable - The variable to format
	 * @return mixed - The formatted output.
	 */
	final public function typeVariable($variable)
	{
		if(is_object($variable))
			return sprintf('Object "%1$s"', get_class($variable));
		if(is_array($variable))
			return sprintf('Array[%1$s]', count($variable));
		if(is_bool($variable))
			return ($variable === true) ? 'true' : 'false';
		return print_r($variable, true);
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
	public function typeVariable($variable);
}
