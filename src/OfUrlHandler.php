<?php
/**
 *
 * @package     OpenFlame Web Framework
 * @copyright   (c) 2010 OpenFlameCMS.com
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 *
 * Minimum Requirement: PHP 5.0.0
 */

if(!defined('ROOT_PATH'))
	define('ROOT_PATH', './');

/**
 * OpenFlame Web Framework - Url Hanlder
 * 	     Allows handing of pretty urls within PHP and increases portability betwween Web Servers.
 *
 *
 * @author      Sam Thompson ("Sam")
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        http://github.com/OpenFlame/OpenFlame-Framework
 */
class OfUrlHandler
{
	/**
	 * Path to the website root, use this when sending URLs to the HTML of the page
	 *
	 * @var string
	 */
	public $web_root_path = '';

	/**
	 * URL base, will be removed from the REQUEST_URI
	 *
	 * @var string
	 */
	private $url_base = '';

	/**
	 * Each of the URL parts in an array
	 *
	 * @var array
	 */
	private $url_parts = array();

	/**
	 * Copy of $this->url_parts to be used in ::get()
	 *
	 * @var array
	 */
	private $_url_parts = array();

	/**
	 * Just to make sure no one sends stupidly long url requests for this to process
	 */
	const EXPLODE_LIMIT = 15;

	/**
	 * Constructor
	 *
	 * @param string $url_base
	 * @return object
	 */
	public function __construct($url_base)
	{
		$this->url_base = (string) $url_base;

		// Add the leading / to the url base
		if($this->url_base[0] !== '/')
			$this->url_base = '/' . $this->url_base;

		// Add the trailing / to the url base
		if(strrpos($this->url_base, '/') !== (strlen($this->url_base) - 1))
			$this->url_base .= '/';

		$url = (string) $_SERVER['REQUEST_URI'];

		// remove the url base from the beginning
		if (strpos($url, $this->url_base) === 0)
			$url = substr($url, strlen($this->url_base) - 1);

		// Get rid of _GET query string
		if (strpos($url, '?') !== false)
			$url = substr($url, 0, strpos($url, '?'));

		// if we have url_append at the end, remove it
		if (substr($url, -1) == '/')
			$url = substr($url, 0, -1);

		// remove / at the beginning
		if (strlen($url) && $url[0] === '/')
			$url = substr($url, 1);

		$url = explode('/', $url, self::EXPLODE_LIMIT);

		for($i = 0; $i < sizeof($url); $i++)
		{
			if(empty($url[$i]))
				continue;

			$this->url_parts[] = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $url[$i]), ENT_COMPAT, 'UTF-8'));

			// Var for all relative linking
			$this->web_root_path .= '../';
		}

		$this->_url_parts = $this->url_parts;
	}

	/**
	 * Build a URL
	 *
	 * @param array $url_ary Array of url chunks, "array('lol', 'asdf', '12345')" would create "/lol/asdf/12345"
	 * @param array $request_ary Array of request data ('id' => '123', 'mode' => 'edit')
	 * @param string $append_string A string that gets appended to the URL (for anchored links)
	 * @param bool $use_amp use &amp; (true) or & (false)
	 * @return string url
	 */
	public function build($url_ary = array(), $request_ary = array(), $append_string = '', $use_amp = true)
	{
		// Find the parts of the URL that are the same from the beginning.
		$congruences = 0;
		foreach($this->url_parts as $i => $element)
		{
			if($url_ary[$i] == $element)
				$congruences++;
		}

		// Get only the parts that are different
		$_url = array_slice($this->url_parts, $congruences);
		$url_ary = array_slice($url_ary, $congruences);

		// Create $url and start building the path
		$url = './';
		for($i = 0; $i < sizeof($_url); $i++)
			$url .= '../';

		// Add the new path
		$url .= implode('/', $url_ary) . (!empty($url_ary) ? '/' : '');

		// Add the _GET params
		if(sizeof($request_ary))
		{
			$url .= '?';
			$_request_ary = array();

			foreach($request_ary as $name => $value)
				$_request_ary[] = $name . '=' . $value;

			$glue = ($use_amp) ? '&amp;' : '&';
			$url .= implode($glue, $_request_ary);
		}

		// Anchor links
		if(!empty($append_string))
			$url .= $append_string;

		return $url;
	}

	/**
	 * Get the next part of the URL (array_shift())
	 *
	 * @param string default value
	 * @return string URL
	 */
	public function get($default = '')
	{
		if (sizeof($this->_url_parts))
		{
			$return = array_shift($this->_url_parts);

			$type = gettype($default);
			settype($return, $type);

			return $return;
		}

		return $default;
	}

	/**
	 * Checks for extranious URL elements not used with url_handler::get().
	 * This allows you to 404 them in your script.
	 *
	 * @return bool
	 */
	public function checkExtra()
	{
		return sizeof($this->_url_parts) ? true : false;
	}
}
