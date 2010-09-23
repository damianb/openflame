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
		'BadJSONNoFile',
	);

	/**
	 * Prepare the test suite.
	 * @return void
	 */
	public function prepareTests()
	{
		require OF_ROOT . 'OfJSON.php';
	}

	protected function testGoodJSON()
	{
		$array = array('key' => 'value', 'subarray' => array('key' => 'value', 'another_value'));
		$json = OfJSON::encode($array);
		try
		{
			$json_success = ($array === OfJSON::decode($json, false));
		}
		catch(OfJSONException $e)
		{
			$json_success = false;
		}
		return $this->expect(__METHOD__, $json_success, true);
	}

	protected function testBadJSONSyntax()
	{
		$bad_json = substr(OfJSON::encode(array('key' => 'value', 'subarray' => array('key' => 'value', 'another_value'))), 5);
		$json_success = false;
		try
		{
			$json = OfJSON::decode($bad_json, false);
		}
		catch(OfJSONException $e)
		{
			$json_success = true;
		}
		return $this->expect(__METHOD__, $json_success, true);
	}

	protected function testBadJSONNoFile()
	{
		$json_success = false;
		try
		{
			$json = OfJSON::decode('./nonexistantfile');
		}
		catch(OfJSONException $e)
		{
			if($e->getCode() === OfJSONException::ERR_JSON_NO_FILE)
				$json_success = true;
		}
		return $this->expect(__METHOD__, $json_success, true);
	}
}
