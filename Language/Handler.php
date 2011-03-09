<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Language;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Language entry handler
 * 	     Manages document language entries and provides access to manipulate them.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Handler
{
	/**
	 * @var array $entries - The language variable entries
	 */
	protected $entries = array();

	/**
	 * Get a language entry.
	 * @param string $entry_key - The language key for the language entry we want.
	 * @return string - Either the language entry we wanted, or the language key we specified if the language entry does not exist.
	 */
	public function getEntry($entry_key)
	{
		// @todo perhaps throw exception on undefined language entry?
		if(!isset($this->entries[$entry_key]))
			return $entry_key;
		return $this->entries[$entry_key];
	}

	/**
	 * Set a new language entry
	 * @param string $entry_key - The language key to set the entry as.
	 * @param string $value - The value of the language entry.
	 * @return OpenFlame\Framework\Language\Handler - Provides a fluent interface.
	 */
	public function setEntry($entry_key, $value)
	{
		$this->entries[$entry_key] = $value;
		return $this;
	}

	/**
	 * Load an array of language entries at once.
	 * @param array $entries - Array of language entries to load.
	 * @return OpenFlame\Framework\Language\Handler - Provides a fluent interface.
	 */
	public function loadEntries(array $entries)
	{
		$this->entries = array_merge($this->entries, $entries);
		return $this;
	}

	/**
	 * Dump out all language entries.
	 * @return array - All the language entries.
	 */
	public function getAllEntries()
	{
		return $this->entries;
	}
}
