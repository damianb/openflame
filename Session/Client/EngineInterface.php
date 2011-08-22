<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  session
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Session\Client;
use \OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Session Client-side identification,
 * 		Sessions client engine prototype
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
interface EngineInterface
{
	/*
	 * Initialize the engine
	 * @param array options - Associative array of options
	 * @return void
	 */
	public function init(&$options);

	/*
	 * Get the SID of the current visitor
	 * @return string - Session ID 
	 */
	public function getSID();

	/*
	 * Set an SID
	 * @param string sid - Session ID to set
	 * @return void
	 */
	public function setSID($sid);
}
