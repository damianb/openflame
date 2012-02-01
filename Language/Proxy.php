<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  language
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Language;

/**
 * OpenFlame Framework - Language proxy object
 * 	     Provides seamless access to language variables from within a Twig template.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class Proxy
{
	/**
	 * @var \emberlabs\openflame\Language\Handler - The language handler which maintains all language entries
	 */
	protected $handler;

	/**
	 * Constructor
	 * @param \emberlabs\openflame\Language\Handler $handler - The language handler
	 * @return void
	 */
	public function __construct(Handler $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * Magic method, providing seamless access to language entries in Twig templates.
	 * @param string $name - The name of the entry to grab.
	 * @return string - The language variable entry we want.
	 */
	public function __get($name)
	{
		return $this->handler->getEntry($name);
	}

	/**
	 * Magic method, providing seamless access to language entries in Twig templates.
	 * @param string $name - The name of the entry to check for existence.
	 * @return boolean - Whether or not the language entry exists.
	 */
	public function __isset($name)
	{
		// @todo look to see if passing TRUE always would be a good idea, as it would allow us to just pass back the language key if the language entry is undefined.
		return ($this->handler->getEntry($name) !== $name) ? true : false;
	}
}
