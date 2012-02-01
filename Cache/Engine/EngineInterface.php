<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  cache
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Cache\Engine;
use \emberlabs\openflame\Event\Instance as Event;

/**
 * OpenFlame Framework - Cache Engine interface,
 * 		Cache engine prototype, declares required methods that a cache engine must define in order to be valid.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
interface EngineInterface
{
	public function getEngineName();
	public function load($key);
	public function exists($key);
	public function destroy($key);
	public function store($key, $data, $ttl);
	public function gc(Event $event = NULL);
}
