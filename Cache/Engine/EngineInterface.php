<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  cache
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Cache\Engine;
use \OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Cache Engine interface,
 * 		Cache engine prototype, declares required methods that a cache engine must define in order to be valid.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
interface EngineInterface
{
	public function getEngineName();
	public function load($key);
	public function exists($key);
	public function destroy($key);
	public function store($key, $data, $ttl);
}
