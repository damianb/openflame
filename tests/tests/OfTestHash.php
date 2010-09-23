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
 * OpenFlame Web Framework - Test class
 * 	    Provides a set of tests to be run.
 *
 *
 * @category    OpenFlame Framework
 * @package     tests
 * @author      Damian Bushong ("Obsidian")
 * @license     MIT License
 */
class OfTestHash extends OfTestBase
{
	/**
	 * @var array - The array of tests to run
	 */
	protected $test_ary = array(
		'GoodHash',
		'BadHash',
	);

	/**
	 * Prepare the test suite.
	 * @return void
	 */
	public function prepareTests()
	{
		require OF_ROOT . 'OfHash.php';
	}

	protected function testGoodHash()
	{
		$hash = new OfHash(8, true);
		$password = $hash->hash('some_password');
		return $this->expect(__METHOD__, $hash->check('some_password', $password), true);
	}

	protected function testBadHash()
	{
		$hash = new OfHash(8, true);
		$password = $hash->hash('some_password');
		return $this->expect(__METHOD__, $hash->check('some_wrong_password', $password), false);
	}
}
