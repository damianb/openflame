<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  twig
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Twig;
use \emberlabs\openflame\Core\Internal\InvalidArgumentException;
use \emberlabs\openflame\Core\Internal\RuntimeException;

/**
 * OpenFlame Framework - Twig management class
 * 	     Basically sets up Twig for use by the OpenFlame Framework.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class Wrapper
{
	/**
	 * @var string - The path to the root directory of twig's include files.
	 */
	protected $twig_root_path = '';

	/**
	 * @var string - The path to the cache directory that we want to use for Twig.
	 */
	protected $twig_cache_path = '';

	/**
	 * @var array - The array of template paths to load from.
	 */
	protected $template_paths = array();

	/**
	 * @var array - The options to set when instantiating the twig environment object
	 */
	protected $twig_environment_options = array();

	/**
	 * @var \Twig_Loader_Filesystem - The twig filesystem loader object.
	 */
	protected $twig_loader;

	/**
	 * @var \Twig_Loader_Chain - The twig chain loader object.
	 */
	protected $twig_chain_loader;

	/**
	 * @var array - Array of additional twig loaders to use (Twig_Loader_Filesystem will always come first)
	 */
	protected $extra_twig_loaders = array();

	/**
	 * @var \Twig_Environment - The twig environment object.
	 */
	protected $twig_environment;

	/**
	 * @var boolean - Has twig been initialized?
	 */
	protected $twig_launched = false;

	/**
	 * Get the current twig root path
	 * @return string - The current twig root path in use.
	 */
	public function getTwigRootPath()
	{
		return $this->twig_root_path;
	}

	/**
	 * Set the root directory for Twig's include files.
	 * @param string $twig_root_path - The root directory that contains Twig's include files (should directly contain the twig autoloader).
	 * @return \emberlabs\openflame\Template\Twig - Provides a fluent interface.
	 *
	 * @throws InvalidArgumentException
	 */
	public function setTwigRootPath($twig_root_path)
	{
		$twig_root_path = rtrim($twig_root_path, '/') . '/';
		if(!is_file($twig_root_path . 'Autoloader.php'))
		{
			throw new InvalidArgumentException(sprintf('Could not locate the Twig autoloader at "%1$s"', $twig_root_path . 'Autoloader.php'));
		}

		$this->twig_root_path = $twig_root_path;

		return $this;
	}

	/**
	 * Get the current twig cache path.
	 * @param string - The current twig cache path in use.
	 */
	public function getTwigCachePath()
	{
		return $this->twig_cache_path;
	}

	/**
	 * Set the cache path to use with twig.
	 * @param string $twig_cache_path - The directory to use as the twig cache path.
	 * @return \emberlabs\openflame\Template\Twig - Provides a fluent interface.
	 *
	 * @throws InvalidArgumentException
	 */
	public function setTwigCachePath($twig_cache_path)
	{
		$twig_cache_path = rtrim($twig_cache_path, '/') . '/';
		if(!is_dir($twig_cache_path))
		{
			throw new InvalidArgumentException(sprintf('The specified Twig cache directory "%1$s" is invalid and either does not exist or is not a usable directory.', $twig_cache_path));
		}

		$this->twig_cache_path = $twig_cache_path;

		return $this;
	}

	/**
	 * Get the full array of currently set twig options.
	 * @return array - The array of twig options that are currently in use.
	 */
	public function getTwigOptions()
	{
		return $this->twig_environment_options;
	}


	/**
	 * Get a specific twig option's value.
	 * @param string $option - The option to grab.
	 * @return mixed - The option's value, or NULL if no such option.
	 */
	public function getTwigOption($option)
	{
		if(!isset($this->twig_environment_options[(string) $option]))
		{
			return NULL;
		}
		else
		{
			return $this->twig_environment_options[(string) $option];
		}
	}

	/**
	 * Set a twig environment option (only use before calling initTwig())
	 * @param string $option - The name of the option to set.
	 * @param mixed $value - The value to set for the option.
	 * @return \emberlabs\openflame\Template\Twig - Provides a fluent interface.
	 */
	public function setTwigOption($option, $value)
	{
		$this->twig_environment_options[(string) $option] = $value;

		return $this;
	}

	/**
	 * Add another loader for use with twig.
	 * @param \Twig_LoaderInterface $loader - The loader to add to the loader chain.
	 * @return \emberlabs\openflame\Template\Twig - Provides a fluent interface.
	 *
	 * @throws RuntimeException
	 */
	public function setTwigLoader(\Twig_LoaderInterface $loader)
	{
		if($this->twig_launched)
		{
			throw new RuntimeException('Cannot add additional twig loaders once the Twig environment has been instantiated');
		}

		$this->extra_twig_loaders[] = $loader;

		return $this;
	}

	/**
	 * Get the current array of template paths that we are using.
	 * @return array - The array of template paths that we will try to load from.
	 */
	public function getTemplatePaths()
	{
		return $this->template_paths;
	}

	/**
	 * Set a new template path for use with Twig.
	 * @param string $template_path - The template path to add.
	 * @return \emberlabs\openflame\Template\Twig - Provides a fluent interface.
	 */
	public function setTemplatePath($template_path)
	{
		$this->template_paths[] = $template_path;

		// Update the template paths in the twig loader
		$this->updateTemplatePaths();

		return $this;
	}

	/**
	 * Update the template paths if twig's been launched.
	 * @return \emberlabs\openflame\Template\Twig - Provides a fluent interface.
	 */
	public function updateTemplatePaths()
	{
		if($this->twig_launched)
		{
			$this->twig_loader->setPaths($this->getTemplatePaths());
		}

		return $this;
	}

	/**
	 * Check to see if we have init'd twig yet
	 * @return boolean - Has twig been init'd?
	 */
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

		$this->twig_loader = new \Twig_Loader_Filesystem($this->getTemplatePaths());

		$options = array_merge(array('cache' => $this->getTwigCachePath()), $this->getTwigOptions());
		if(!empty($this->extra_twig_loaders))
		{
			$this->twig_chain_loader = new \Twig_Loader_Chain(array_merge(array($this->twig_loader), $this->extra_twig_loaders));
			$this->twig_environment = new \Twig_Environment($this->twig_chain_loader, $options);
		}
		else
		{
			$this->twig_environment = new \Twig_Environment($this->twig_loader, $options);
		}

		$this->twig_launched = true;

		return $this->twig_environment;
	}
}
