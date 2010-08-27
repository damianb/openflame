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
 * @author      David King ("imkingdavid")
 * @license     MIT License
 */
class OfTestInput extends OfTestBase
{
	/**
	 * @var array - The array of tests to run
	 */
	protected $test_ary = array(
		'GoodURL',
		'BadURL',
	);

	/**
	 * Prepare the test suite.
	 * @return void
	 */
	public function prepareTests()
	{
		require OF_ROOT . 'OfInput.php';

            /**
             * @note - These all use _POST, simply to check the regex.
             *         Separate tests will need to be run to check how it handles not having the proper input type
             *         (such as looking for _POST but having _GET or otherwise)
             */
                $_POST['url'] = 'http://www.openflamecms.com/';
                $_POST['url_bad'] = 'www. somewhere far-away.domain';

                $_POST['email'] = 'imkingdavid@phpbb.com';
                $_POST['email_bad'] = 'not_an@email, duh!.thing';

                $_POST['alphanumeric'] = 'a1b2c3d4e5f6g7h8i9j10k11l12m13n14o15p16q17r18s19t20u21v22w23x24y25z26';
                $_POST['alphanumeric_bad'] = 's0m3_4lph4num3r!c_w!7h_5ymb015';
	}

	/**
	 * Get our input object
	 * @return OfInput - our input object
	 */
	protected function getInput($var_name, $default, $global_name)
	{
		return new OfInput($var_name, $default, $global_name);
	}

	protected function testGoodURL()
	{
		$input = $this->getInput('url', '', '_POST');
		return $this->expect('valid url', $input->validate('url'), true);
	}

	protected function testBadURL()
	{
		$input = $this->getInput('url_bad', '', '_POST');
		return $this->expect('invalid url', $input->validate('url'), false);
	}

        protected function testGoodEmail()
        {
                $input = $this->getInput('email', '', '_POST');
                return $this->expect('valid email', $input->validate('email'), true);
        }

        protected function testBadEmail()
        {
                $input = $this->getInput('email_bad', '', '_POST');
                return $this->expect('invalid email', $input->validate('email'), false);
        }

        protected function testGoodAlphaNumeric()
        {
                $input = $this->getInput('alphanumeric', '', '_POST');
                return $this->expect('valid alphanumeric string', $input->validate('alphanumeric'), true);
        }

        protected function testBadAlphaNumeric()
        {
                $input = $this->getInput('alphanumeric_bad', '', '_POST');
                return $this->expect('invalid alphanumeric string', $input->validate('alphanumeric'), false);
        }
}
