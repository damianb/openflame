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
	/**
	 * Set options
	 * @param array - Key/value pair array for all client-id level config options
	 */
	public function setOptions($options);

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
}
