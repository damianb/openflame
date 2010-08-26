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
class OfTestJSON extends OfTestBase
{
	/**
	 * @var array - The array of tests to run
	 */
	protected $test_ary = array(
		'GoodJSON',
		'BadJSONSyntax',
		'BadJSONNoFile', // do this
	);

	/**
	 * Prepare the test suite.
	 * @return void
	 */
	public function prepareTests() { }

	protected function testGoodJSON()
	{
		$password = $hash->hash('some_password');
		return $this->expect('valid password', $hash->check($password, 'some_password'), true);
	}

	protected function testBadJSONSyntax()
	{
		$bad_json = substr(OfJSON::encode(array('key' => 'value', 'subarray' => array('key' => 'value', 'another_value'))), 5);
		try
		{
			$json = OfJSON::decode($bad_json, false);
			$json_success = true;
		}
		catch(OfJSONException $e)
		{
			$json_success = false;
		}
		return $this->expect('invalid json', $json_success, false);
	}
}
