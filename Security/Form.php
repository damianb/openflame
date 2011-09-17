<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  security
 * @copyright   (c) 2010 - 2011 openflame-project.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace OpenFlame\Framework\Security;
use OpenFlame\Framework\Core;

/**
 * OpenFlame Framework - Form validation and CSRF protection
 * 	     Provides a coherent random seed or string for use in applications.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Form
{
	/**
	 * @var integer - The time that the object was instantiated
	 */
	protected $time = 0;

	/**
	 * @var string - The seed to use when generating form keys.
	 */
	protected $seed = '';

	/**
	 * @var boolean - Is the form seed locked in?
	 */
	private $lock_seed = false;

	/**
	 * Constructor
	 * @return void
	 */
	public function __construct()
	{
		$this->time = time();
	}

	/**
	 * Get the form timestamp to use.
	 * @return integer - The UNIX timestamp to use for the form timestamp.
	 */
	public function getFormTime()
	{
		return $this->time;
	}

	/**
	 * Get the form seed in use.
	 * @return string - The form seed in use.
	 */
	public function getFormSeed()
	{
		return $this->seed;
	}

	/**
	 * Set the form seed to use for form key generation.
	 * @param string $seed - The seed to generate.
	 * @return \OpenFlame\Framework\Security\Form - Provides a fluent interface.
	 * @note WARNING, it is not possible to alter the form seed once it has been set!
	 *
	 * @throws \RuntimeException
	 */
	final public function setFormSeed($seed)
	{
		if($this->lock_seed === true)
		{
			throw new \RuntimeException('Cannot overwrite form seed, form seed is locked');
		}

		$this->seed = (string) $seed;

		return $this;
	}

	/**
	 * Check to see if the form submitted is valid.
	 * @param string $form_key - The form key submitted.
	 * @param string $form_time - The time the form was generated.
	 * @param string $form_name - The name of the form.
	 * @return boolean - Is the form key valid?
	 */
	final public function checkFormKey($form_key, $form_time, $form_name)
	{
		// Lock in the current seed.
		$this->lock_seed = true;

		return ($this->createKey($form_time, $this->seed, $form_name) === $form_key) ? true : false;
	}

	/**
	 * Build a form key for use in-template.
	 * @param string $form_name - The name of the form to build the form key for.
	 * @return string - The form key created for the specified form.
	 */
	final public function buildFormKey($form_name)
	{
		// Lock in the current seed.
		$this->lock_seed = true;

		return $this->createKey($this->time, $this->seed, $form_name);
	}

	/**
	 * Create a form key out of the specified seed, form name and timestamp.
	 * @param integer $time - The timestamp to generate the form with.
	 * @param string $seed - The string seed for building the form key with.
	 * @param string $form_name - The name of the form.
	 * @return string - The generated form key.
	 */
	protected function createKey($time, $seed, $form_name)
	{
		return substr(hash('md5', (int) $time . '|' . trim($seed) . '_' . trim($form_name)), 0, 12);
	}
}
