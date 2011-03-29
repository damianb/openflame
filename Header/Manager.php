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
	protected $headers = array();

	protected $headers_sent = false;

	protected $submodules = array();

	public function setHeader($header_name, $header_value)
	{
		$this->headers[(string) $header_name] = (string) $header_value;

		return $this;
	}

	public function isHeaderSet($header_name)
	{
		return (bool) isset($this->headers[(string) $header_name]);
	}

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

	public function getHeadersDump()
	{
		return $this->headers;
	}

	public function getHeadersDumpAsString()
	{
		foreach($this->headers as $name => $value)
		{
			$headers[] = sprintf('%1$s: %2$s', $name, $value);
		}
		return implode("\n", $headers);
	}

	public function sendHeaders()
	{
		foreach($this->headers as $name => $value)
		{
			header(sprintf('%1$s: %2$s', $name, $value), true);
		}
	}

	public function getSubmodule($submodule)
	{
		if(!isset($this->submodules[$submodule]))
		{
			throw new \RuntimeException(sprintf('Failed to retreive submodule, submodule not loaded'));
		}

		return $this->submodules[$submodule];
	}

	public function loadSubmodule($submodule)
	{
		$submodule_class = "\\OpenFlame\Framework\\Header\\Submodule\\$submodule";

		// Check to see if the submodule actually does exist
		if(!class_exists($submodule_class, true))
		{
			throw new \RuntimeException(sprintf('Failed to load header submodule "%1$s", submodule does not exist', $submodule_class));
		}

		$this->submodules[$submodule] = new $submodule_class();

		return $this->submodules[$submodule];
	}

	public function __isset($name)
	{
		return (bool) isset($this->submodules[$submodule]);
	}

	public function __get($name)
	{
		if(!isset($this->submodules[$submodule]))
		{
			throw new \RuntimeException(sprintf('Failed to retreive submodule, submodule not loaded'));
		}

		return $this->submodules[$submodule];
	}
}
