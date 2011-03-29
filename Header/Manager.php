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

namespace OpenFlame\Framework\Header;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Web Framework - Header manager object
 * 	     Takes in and manages headers that should be sent upon page display.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/OpenFlame/OpenFlame-Framework
 */
class Manager
{
	/**
	 * @var array - The array of header data that the header manager is storing
	 */
	protected $headers = array();

	/**
	 * @var boolean - Has the header manager sent its own headers?
	 */
	protected $headers_sent = false;

	/**
	 * @var array - Array of header management submodules, which provide extended functionality in managing specific subsets of headers (such as redirects, cookies)
	 */
	protected $submodules = array();

	/**
	 * Grab all the current headers defined, store them internally, and then trash them so that only the headers stored in the manager are the ones that will be sent.
	 * @return void
	 */
	public function snagHeaders()
	{
		// grab all current headers
		$headers = headers_list();
		for($i = 0, $size = sizeof($headers); $i <= $size; $i++)
		{
			// store the current headers
			list($header_name, $header_value) = explode(': ', $headers[$i], 2);
			$this->setHeader($header_name, $header_value);
		}

		// Trash all previously sent headers.  We're now in full control of headers sent.
		header_remove();
	}

	/**
	 * Has a specific header been set?
	 * @param string $header_name - The name of the header to check existence of.
	 * @return boolean - Will the specified header be sent?
	 */
	public function isHeaderSet($header_name)
	{
		return (bool) isset($this->headers[(string) $header_name]);
	}

	/**
	 * Get a specific header.
	 * @param string $header_name - The name of the header to grab.
	 * @return mixed - An array containing the name and value of the header obtained, or NULL if it doesn't exist.
	 */
	public function getHeader($header_name)
	{
		if(isset($this->headers[(string) $header_name]))
		{
			return array($header_name => $this->headers[(string) $header_name]);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Get a specific header as a string (in the format header() would expect).
	 * @param string $header_name - The name of the header to grab.
	 * @return mixed - The header string in the format header() would expect, or NULL if it doesn't exist.
	 */
	public function getHeaderAsString($header_name)
	{
		if(isset($this->headers[(string) $header_name]))
		{
			return sprintf('%1$s: %2$s', $header_name, $this->headers[(string) $header_name]);
		}
		else
		{
			return NULL;
		}
	}

	/**
	 * Set a header (or override its previous value)
	 * @param string $header_name - The name of the header to set.
	 * @param string $header_value - The value to set for the header.
	 * @return \OpenFlame\Framework\Header\Manager - Provides a fluent interface.
	 *
	 * @todo see if allowing duplicate headers to be sent should be allowed
	 */
	public function setHeader($header_name, $header_value)
	{
		$this->headers[(string) $header_name] = (string) $header_value;

		return $this;
	}

	/**
	 * Remove/unset a specific header
	 * @param string $header_name - The name of the header to remove.
	 * @return \OpenFlame\Framework\Header\Manager - Provides a fluent interface.
	 */
	public function removeHeader($header_name)
	{
		unset($this->headers[(string) $header_name]);

		return $this;
	}

	/**
	 * Have the headers stored by the manager been sent?
	 * @param boolean $internal_headers_only - Specify false to see if *any* headers have been sent at all.
	 * @return boolean - Have the headers been sent?
	 */
	public function headersSent($internal_headers_only = true)
	{
		if($internal_headers_only === true)
		{
			return (bool) $this->headers_sent;
		}
		else
		{
			return (bool) headers_sent();
		}
	}

	/**
	 * Get an array of the headers that the manager is storing.
	 * @return array - The headers currently being stored.
	 */
	public function getHeadersDump()
	{
		return $this->headers;
	}

	/**
	 * Get the full string of headers that are being stored (individual entries joined by unix newline)
	 * @return string - The string containing all headers (represented as strings) that are currently being stored.
	 */
	public function getHeadersDumpAsString()
	{
		foreach($this->headers as $name => $value)
		{
			$headers[] = $this->getHeaderAsString($name);
		}
		return implode("\n", $headers);
	}

	/**
	 * Send all of the currently stored headers.
	 * @return \OpenFlame\Framework\Header\Manager - Provides a fluent interface.
	 */
	public function sendHeaders()
	{
		foreach($this->headers as $name => $value)
		{
			header(sprintf('%1$s: %2$s', $name, $value), true);
		}
		$this->headers_sent = true;

		return $this;
	}

	/**
	 * Is a header management submodule loaded?
	 * @param string $submodule - The submodule to check.
	 * @return boolean - Is the specified submodule currently loaded?
	 */
	public function isSubmoduleLoaded($submodule)
	{
		return (bool) isset($this->submodules[$submodule]);
	}

	/**
	 * Get a loaded header management submodule
	 * @param string $submodule - The name of the submodule to grab.
	 * @return \OpenFlame\Framework\Header\Submodule\SubmoduleInterface - The submodule requested.
	 *
	 * @throws \RuntimeException
	 */
	public function getSubmodule($submodule)
	{
		if(!isset($this->submodules[$submodule]))
		{
			throw new \RuntimeException(sprintf('Failed to retreive submodule, submodule not loaded'));
		}

		return $this->submodules[$submodule];
	}

	/**
	 * Load a header management submodule
	 * @param string $submodule - The submodule to load.
	 * @return \OpenFlame\Framework\Header\Submodule\SubmoduleInterface - The submodule just loaded.
	 *
	 * @throws \RuntimeException
	 * @throws \LogicException
	 */
	public function loadSubmodule($submodule)
	{
		$submodule_class = "\\OpenFlame\Framework\\Header\\Submodule\\$submodule";

		// Check to see if the submodule actually does exist
		if(!class_exists($submodule_class, true))
		{
			throw new \RuntimeException(sprintf('Failed to load header management submodule "%1$s"; submodule does not exist', $submodule_class));
		}

		$submodule_object = $submodule_class::newInstance();
		if(!($submodule_object instanceof \OpenFlame\Framework\Header\Submodule\SubmoduleInterface))
		{
			throw new \LogicException(sprintf('Header management submodule "%1$s" does not implement \\OpenFlame\\Framework\\Header\\Submodule\\SubmoduleInterface as required.'));
		}

		// Store the header manager in the submodule, and store the submodule.
		$submodule_object->setManager($this);
		$this->submodules[$submodule] = $submodule_object;

		return $this->submodules[$submodule];
	}

	/**
	 * Is a header management submodule loaded?
	 * @param string $submodule - The submodule to check.
	 * @return boolean - Is the specified submodule currently loaded?
	 */
	public function __isset($submodule)
	{
		return (bool) isset($this->submodules[$submodule]);
	}

	/**
	 * Get a loaded header management submodule
	 * @param string $submodule - The name of the submodule to grab.
	 * @return \OpenFlame\Framework\Header\Submodule\SubmoduleInterface - The submodule requested.
	 *
	 * @throws \RuntimeException
	 */
	public function __get($submodule)
	{
		if(!isset($this->submodules[$submodule]))
		{
			throw new \RuntimeException(sprintf('Failed to retreive submodule, submodule not loaded'));
		}

		return $this->submodules[$submodule];
	}
}
