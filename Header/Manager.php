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

namespace OpenFlame\Framework\Header;
use OpenFlame\Framework\Core;

if(!defined('OpenFlame\\ROOT_PATH')) exit;

/**
 * OpenFlame Framework - Header manager object
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
	 * @var integer - The HTTP status header to use for the current page.
	 */
	protected $http_status = 200;

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
		for($i = 0, $size = sizeof($headers); $i < $size; $i++)
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
	public function getHeaders($header_name)
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
	 * Set a header (or override its previous value)
	 * @param string $header_name - The name of the header to set.
	 * @param string $header_value - The value to set for the header.
	 * @return \OpenFlame\Framework\Header\Manager - Provides a fluent interface.
	 */
	public function setHeader($header_name, $header_value)
	{
		if(!isset($this->headers[(string) $header_name]))
		{
			$this->headers[(string) $header_name] = array();
		}

		$this->headers[(string) $header_name][] = (string) $header_value;

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
	 * Get the current HTTP status header code.
	 * @return integer - The current HTTP status code.
	 */
	public function getHTTPStatus()
	{
		return $this->http_status;
	}

	/**
	 * Get the current HTTP status header code.
	 * @return string - The current HTTP status code in the full HTTP header format.
	 *
	 * @throw \LogicException
	 */
	public function getHTTPStatusHeader()
	{
		$server_errors = array(
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			204 => 'No Content',
			205 => 'Reset Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found', // Moved Temporarily
			303 => 'See Other',
			304 => 'Not Modified',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			403 => 'Forbidden',
			404 => 'Not Found',
			406 => 'Not Acceptable',
			409 => 'Conflict',
			410 => 'Gone',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
		);

		// Make sure we know what to use for this HTTP status.
		if(!isset($server_errors[$this->http_status]))
		{
			throw new \LogicException(sprintf('Unrecognized HTTP status code "%d"', $this->http_status));
		}

		return sprintf('HTTP/1.0 %1$d %2$s', $this->http_status, $server_errors[$this->http_status]);
	}

	/**
	 * Set the current HTTP status code.
	 * @param integer $http_status - Set the HTTP status code.
	 * @return \OpenFlame\Framework\Header\Manager - Provides a fluent interface.
	 */
	public function setHTTPStatus($http_status)
	{
		$this->http_status = $http_status;

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
	 * Send all of the currently stored headers.
	 * @return \OpenFlame\Framework\Header\Manager - Provides a fluent interface.
	 */
	public function sendHeaders()
	{
		// Get the submodules to inject their headers
		foreach($this->submodules as $submodule)
		{
			$submodule->injectHeaders();
		}

		header($this->getHTTPStatusHeader());
		foreach($this->headers as $header_name => $header_array)
		{
			foreach($header_array as $header)
			{
				header(sprintf('%1$s: %2$s', $header_name, $header), false);
			}
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
	 */
	public function getSubmodule($submodule)
	{
		if(!isset($this->submodules[$submodule]))
		{
			return $this->loadSubmodule($submodule);
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
