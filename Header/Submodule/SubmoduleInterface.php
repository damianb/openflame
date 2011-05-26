<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  header
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Header\Submodule;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Framework - Header management submodule interface,
 * 	     Makes sure that we have the methods necessary to manipulate the header management submodule object.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
interface SubmoduleInterface
{
	public static function newInstance();
	public function setManager(\OpenFlame\Framework\Header\Manager $manager);
	public function injectHeaders();
}
