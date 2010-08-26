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
	public function prepareTests() { }
	
	/**
	 * Get our password hashing object
	 * @return OfHash - our password hashing object
	 */
	protected function getHash()
	{
		return new OfHash(8, true);
	}

	protected function testGoodHash()
	{
		$hash = $this->getHash();
		$password = $hash->hash('some_password');
		return $this->expect('valid password', $hash->check($password, 'some_password'), true);
	}

	protected function testBadHash()
	{
		$hash = $this->getHash();
		$password = $hash->hash('some_password');
		return $this->expect('invalid password', $hash->check($password, 'some_wrong_password'), false);
	}
}
