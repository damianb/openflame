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

namespace OpenFlame\Framework\Security;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Random string/seed generator
 * 	     Provides a coherent random seed or string for use in applications.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Seeder
{
	protected $last_seed = '';
	
	protected $last_seed_time = 0;
	
	protected $last_seed_diff = '';
	
	protected $session_seed = '';
	
	protected $application_seed = '';
	
	protected $instance_seed_count = 0;
}
