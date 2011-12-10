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
 * OpenFlame Framework - ACL binary auth compiler object
 * 	     Takes provided auth flags, group auth settings & inheritances and translates them into a raw auth string for use with the ACL\Auth class.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Translator
{
	/**
	 * @var array - Array containing the finished, processed group data after resolveAuths() is run
	 */
	protected $groups = array();

	/**
	 * @var array - Array of raw ACL processing data used internally
	 */
	protected $group_auth_cache = array();

	/**
	 * @var array - Array of raw group data (like auth cache and )
	 */
	protected $groups_raw = array();

	/**
	 * @var array - Array of all ACL flags in the application. (DO NOT OMIT ANY FLAGS WHATSOEVER)
	 */
	protected $auths = array();

	/**
	 * Load in group data.
	 * @param mixed $group_id - The unique identifier for this group
	 * @param array $group_flags - The array of flags to use for this group.
	 * @param mixed $inherit_id - The identifier of the group that this group inherits auths from, if any.
	 * @param string $auth_cache - The previously generated 'inherit' string, if available (a string comprised of just the character set 0, 1, and 2)
	 * @return \OpenFlame\Framework\ACL\Translator - Provides a fluent interface.
	 */
	public function setGroup($group_id, array $group_flags, $inherit_id = NULL, $auth_cache = NULL)
	{
		$this->groups_raw[$group_id] = array(
			'flags'		=> $group_flags,
			'inherit'	=> $inherit_id,
			//'authcache'	=> $auth_cache,
		);

		if(!empty($auth_cache))
		{
			$this->group_auth_cache[$group_id] = array_map('intval', str_split($auth_cache, 1));
		}

		return $this;
	}

	/**
	 * Load in the array of auths in use in the application.
	 * @param array $auth_flags - Array of all the ACL flags in the application. (DO NOT OMIT ANY FLAGS WHATSOEVER)
	 * @return \OpenFlame\Framework\ACL\Translator - Provides a fluent interface.
	 *
	 * @note If any flags are added, removed, or their order changes, all compiled ACL flag strings must be recomputed.
	 */
	public function setAuths(array $auth_flags)
	{
		$this->auths = array_flip($auth_flags);
	}

	/**
	 * Compute the group auths for the given group or groups (while accounting for group inheritance)
	 * @param mixed - The unique group identifier, or array of unique group identifiers to compute the group auths for.
	 * @return array - An array containing two entries, a "groups" subarray containing the "inherit" and "auths" strings, and potentially the unique identifiers of any child groups (under "children") and the unique identifier of the parent group (under "parent")
	 *
	 * @note if the group's data is not loaded with this->setGroup() the group's auth data will not be computed.
	 */
	public function buildGroupAuths($group_id_set)
	{
		// Ensure this is a workable array of groups to process
		if(!is_array($group_id_set))
		{
			$group_id_set = array($group_id_set);
		}

		foreach($group_id_set as $group_id)
		{
			// Only groups that we have the flag data for please.
			if(!isset($this->groups_raw[$group_id]))
			{
				continue;
			}

			// Handle group auth inheritance
			if(!empty($this->groups_raw[$group_id]['inherit']) && isset($this->group_auth_cache[$this->groups_raw[$group_id]['inherit']]))
			{
				// Mark this group's parent.
				$this->groups[$group_id]['parent'] = $this->groups_raw[$group_id]['inherit'];

				// Mark this group as a child of the one we're inheriting auths from.
				$this->groups[$this->groups_raw[$group_id]['inherit']]['children'][] = $group_id;

				// Let's do it for ALL the ancestors - that way, a change upstream will result in a very easy invalidation of all children.
				$inherit_tree = array();
				$inherit_id = $this->groups[$group_id]['parent'];
				while(true)
				{
					$inherit_id = isset($this->groups[$inherit_id]['parent']) ? $this->groups[$inherit_id]['parent'] : NULL;
					if($inherit_id === NULL)
					{
						break;
					}
					if(isset($inherit_tree[$inherit_id]))
					{
						throw new \RuntimeException('Recursive group inheritance detected, aborting');
					}

					$inherit_tree[$inherit_id] = true;
					$this->groups[$inherit_id]['children'][] = $group_id;
				}

				// Grab the auths of the parent.
				$auth_array = $this->group_auth_cache[$this->groups_raw[$group_id]['inherit']];
			}
			else
			{
				// just create a ton of zeroes for our auth array.
				$auth_array = array_fill(0, count($this->auths), 0);
			}

			$parsed_auth_map = array_combine(array_map(array($this, 'getAuthID'), array_keys($this->groups_raw[$group_id]['flags'])), array_values($this->groups_raw[$group_id]['flags']));
			array_walk($auth_array, array($this, 'blender'), $parsed_auth_map);

			$this->group_auth_cache[$group_id] = $auth_array;

			/**
			 * Build the two "packed" ACL strings here
			 *
			 * note: inherit's string uses 0 for no, 1 for yes, 2 for never - it is used to "cache" in string form the computated access control list for the group for future changes
			 *       auth's string is pure 0/1, yes/no logic for whether a given auth flag is true for the group or not.
			 */
			$this->groups[$group_id]['inherit'] = implode('', str_replace(-1, 2, $auth_array));
			$this->groups[$group_id]['auth'] = implode('', str_replace(array(-1, 2), array(0, 0), $auth_array));
		}

		return array(
			'groups'	=> $this->groups,
			'auths'		=> $this->auths,
		);
	}

	/**
	 * @ignore
	 */
	public function getAuthID($auth_name)
	{
		return $this->auths[$auth_name];
	}

	/**
	 * @ignore
	 */
	public function blender(&$item, $key, $merge)
	{
		/**
		 * assume:
		 * 1  = 'yes'
		 * 0  = 'no'
		 * -1 = 'never'
		 *
		 * only 'yes' will result in ACL bool(true), while both 'no' and 'never' will result in ACL bool(false)
		 *
		 * 'no' will occur if both current and inherited values are '0'
		 * 'no' (inherited as 'never') will occur if either the current or inherited value is -1
		 * 'yes' can result in 'yes' so long as either the current or inherited value has is 1, and neither value is -1
		 *
		 * in practice:
		 *
		 *  (inherited ACL flag setting) & (specified ACL flag setting) -> (resulting ACL flag setting)
		 *
		 * inheriting 0:
		 *   0  & 0  -> 0  (false)
		 *   0  & 1  -> 1  (true)
		 *   0  & -1 -> -1 (false)
		 *
		 * inheriting -1:
		 *   -1 & 0  -> -1 (false)
		 *   -1 & 1  -> -1 (false)
		 *   -1 & -1 -> -1 (false)
		 *
		 * inheriting 1:
		 *   1  & 0  -> 1  (true)
		 *   1  & 1  -> 1  (true)
		 *   1  & -1 -> -1 (false)
		 *
		 * this structure allows "never" to always, ALWAYS work.
		 */
		if(isset($merge[$key]) && $merge[$key] != 0 && $item != -1)
		{
			$item = $merge[$key];
		}
	}
}
