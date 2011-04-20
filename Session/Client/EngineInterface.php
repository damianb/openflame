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

namespace OpenFlame\Framework\Session\Client;
use \OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Session Client-side identification,
 * 		Sessions client engine prototype
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
interface EngineInterface
{
	/**
	 * Get params as they were accepted from the client
	 * @return array - Structure: 'sid' => '', 'uid' => '', 'autologinkey' => ''
	 */
	public function getParams();

	/**
	 * Set params to be stored by the client
	 * @param array - Structure: 'sid' => '', 'uid' => '', 'autologinkey' => ''
	 * @return void
	 */
	public function setParams($params);

	/**
	 * Code to run at the end of the start() method in the driver
	 * @return void
	 */
	public function onStart();

	/**
	 * Code to run at the end of the login() method in the driver
	 * @return void
	 */
	public function onLogin();

	/**
	 * Code to run at the end of the kill() method in the driver
	 * @return void
	 */
	public function onKill();
}
