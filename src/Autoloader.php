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

namespace OpenFlame\Framework;

if(!defined('OpenFlame\\Framework\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Autoloader object
 * 	     Provides just-in-time class autoloading functionality.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Autoloader
{
	/**
	 * @var string - The path that the autoloader will attempt to load files from.
	 */
	private $include_path = '';

	/**
	 * Constructor
	 * @return void
	 */
	protected function __construct()
	{
		// Unless PHAR packaging mode is enabled, we want to grab straight from the src/ folder.
		if(!defined('OpenFlame\\Framework\\USE_PHAR'))
		{
			$this->include_path  = 'phar://' . OpenFlame\Framework\PHAR_PATH;
		}
		else
		{
			$this->include_path = OpenFlame\Framework\ROOT_PATH . '/src';
		}

		$this->include_path = rtrim($this->include_path, '/') . '/';
	}

	/**
	 * Autoload callback for loading class files.
	 * @param string $class - Class to load
	 * @return void
	 */
	public function loadFile($class)
	{
		$class = ($class[0] == '\\') ? substr($class, 1) : $class;

		// Only load Docile's own classes.
		if(substr($class, 0, 20) !== 'OpenFlame\\Framework\\')
			return false;

		$name = $this->cleanName($class);

		// file_exists() seems to be finicky in PHARs, so we don't rely on it if we're in PHAR packaging mode.
		if(!defined('OpenFlame\\Framework\\USE_PHAR') && !file_exists($this->include_path . $name . '.php'))
			return false;

		if(!defined('OpenFlame\\Framework\\DEBUG'))
		{
			@include $this->include_path . $name . '.php';
		}
		else
		{
			@include $this->include_path . $name . '.php';
		}

		if(!class_exists($class, false))
			return false;
		return true;
	}

	/**
	 * Drop the Failnet base namespace if it is there, and replace any backslashes with slashes.
	 * @param string $class_name - The name of the class to spit-polish.
	 * @return string - The cleaned class name.
	 */
	public function cleanName($class)
	{
		$class = (substr($class, 0, 19) == 'OpenFlame\\Framework') ? substr($class, 6) : $class;
		return str_replace('\\', '/', $class);
	}

	/**
	 * Register this class as an autoloader within the autoloader stack.
	 * @return Docile\Autoloader - The newly created autoloader instance.
	 */
	public static function register()
	{
		$self = new self();
		spl_autoload_register(array($self, 'loadFile'));
		return $self;
	}
}
