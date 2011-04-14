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

namespace OpenFlame\Framework\Template;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Twig management class
 * 	     Basically sets up Twig for use by the OpenFlame Framework.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Twig
{
	protected $twig_root_path = '';

	protected $twig_cache_path = '';

	protected $template_paths = array();

	protected $twig_environment_options = array();

	protected $twig_loader;

	protected $twig_environment;

	protected $twig_launched = false;

	public function getTwigRootPath()
	{
		return $this->twig_root_path;
	}

	public function setTwigRootPath($twig_root_path)
	{
		$twig_root_path = rtrim($twig_root_path, '/') . '/';
		if(!is_file($twig_root_path . 'Autoloader.php'))
		{
			throw new \InvalidArgumentException(sprintf('Could not locate the Twig autoloader at "%1$s"', $twig_root_path . '/lib/Twig/Autoloader.php'));
		}

		$this->twig_root_path = $twig_root_path;

		return $this;
	}

	public function getTwigCachePath()
	{
		return $this->twig_cache_path;
	}

	public function setTwigCachePath($twig_cache_path)
	{
		$twig_cache_path = rtrim($twig_cache_path, '/') . '/';
		if(!is_dir($twig_cache_path))
		{
			throw new \InvalidArgumentException(sprintf('The specified Twig cache directory "%1$s" is invalid and either does not exist or is not a usable directory.', $twig_cache_path));
		}

		$this->twig_cache_path = $twig_cache_path;

		return $this;
	}

	public function getTwigOptions()
	{
		return $this->twig_environment_options;
	}

	public function getTwigOption($option)
	{
		if(!isset($this->twig_environment_options[$option]))
		{
			return NULL;
		}
		else
		{
			return $this->twig_environment_options[$option];
		}
	}

	public function setTwigOption($option, $value)
	{
		$this->twig_environment_options[(string) $option] = $value;

		return $this;
	}

	public function getTemplatePaths()
	{
		return $this->template_paths;
	}

	public function setTemplatePath($template_path)
	{
		$this->template_paths[] = $template_path;

		// Update the template paths in the twig loader
		$this->updateTemplatePaths();

		return $this;
	}

	public function updateTemplatePaths()
	{
		if($this->hasTwigLaunched())
		{
			$loader = Core::getObject('twig.loader');
			$loader->setPaths($this->getTemplatePaths());
		}

		return $this;
	}

	public function hasTwigLaunched()
	{
		return (bool) $this->twig_launched;
	}

	/**
	 * Get the twig filesystem loader
	 * @return mixed - NULL if twig filesystem loader isn't present, or object of class \Twig_Loader_Filesystem if twig has been init'd.
	 */
	public function getTwigLoader()
	{
		return $this->twig_loader;
	}

	/**
	 * Get the twig environment object
	 * @return mixed - NULL if twig environment object isn't present, or object of class \Twig_Environment if twig has been init'd.
	 */
	public function getTwigEnvironment()
	{
		return $this->twig_environment;
	}

	/**
	 * Init twig with the provided settings.
	 * @return \Twig_Environment - The twig environment object.
	 */
	public function initTwig()
	{
		require $this->getTwigRootPath() . 'Autoloader.php';
		\Twig_Autoloader::register();

		$this->twig_loader = Core::setObject('twig.loader', new \Twig_Loader_Filesystem($this->getTemplatePaths()));
		$this->twig_environment = Core::setObject('twig.environment', new \Twig_Environment($this->twig_loader, array_merge(array('cache' => $this->getTwigCachePath()), $this->getTwigOptions())));

		$this->twig_launched = true;

		return $this->twig_environment;
	}
}
