<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  ACL
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\ACL;
use \OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - ACL binary auth interpreter object
 * 	     Resolves auth flags and determines if group specified has auth specified by a given auth flag.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Auth
{
	/**
	 * @var array - Array of group auth string data, exactly as compiled by the Translator class.
	 */
	protected $group = array();

	/**
	 * @var array - Array of auth flags, exactly as compiled by the Translator class.
	 */
	protected $auth = array();

	/**
	 * Constructor
	 * @param array $auth_data - Array of auth flags as compiled by the Translator class.
	 * @param array $group_data - Array of group data, as provided by the Translator class (optional, can be loaded later)
	 */
	public function __construct(array $auth_data, array $group_data = NULL)
	{
		$this->auth = $auth_data;

		if($group_data !== NULL)
		{
			foreach($group_data as $group_id => $data)
			{
				$this->addGroupData($group_id, $data);
			}
		}
	}

	/**
	 * Load group ACL data
	 * @param mixed $group_id - The identifier for this group.
	 * @param array $group_data - The ACL data for this group.
	 * @return \OpenFlame\Framework\ACL\Auth - Provides a fluent interface.
	 */
	public function addGroupData($group_id, array $group_data)
	{
		$this->group[$group_id] = $group_data;

		return $this;
	}

	/**
	 * Check a flag against the group specified to see if the group has the specified ACL flag set.
	 * @param mixed $group_id - The identifier for the group to check.
	 * @param mixed $auth - Either a string or an array of strings of the auth flags to check.
	 * @return boolean - Returns false if the group ID isn't recognized, if the ACL flag does not exist, or if any of the auth flag checks fails.  Returns true only if all auth flag checks pass.
	 *
	 * @note - All auth flags can be prefixed with a "!" to invert the lookup - ex: $acl->check(array('SOME_PERMISSION', '!OTHER_PERMISSION'));
	 */
	public function check($group_id, $auth)
	{
		if(!isset($this->group[$group_id]))
		{
			return false;
		}

		if(is_array($auth))
		{
			foreach($auth as $_auth)
			{
				if(!$this->_check($group_id, $_auth))
				{
					return false;
				}
			}

			return true;
		}
		else
		{
			return $this->_check($group_id, $auth);
		}
	}

	/**
	 * Protected - checks the specified auth
	 * @param mixed $group_id - The identifier for the group to check.
	 * @param string $auth - The auth string to check.
	 * @return boolean - Returns false if the ACL flag does not exist or if the auth flag check fails.  Returns true only if the auth flag check passes.
	 */
	protected function _check($group_id, $auth)
	{
		$not = false;
		if($auth[0] == '!')
		{
			$not = true;
			$auth = substr($auth, 1);
		}

		if(!isset($this->auth[$auth]))
		{
			return false;
		}

		$authstring = $this->group[$group_id]['auth'];
		$auth_id = $this->auth[$auth];
		if(!$not)
		{
			return ($authstring[$auth_id]) ? true : false;
		}
		else
		{
			return ($authstring[$auth_id]) ? false : true;
		}
	}
}
