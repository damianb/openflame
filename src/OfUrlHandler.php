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
	public $webRootPath = '';

	/**
	 * URL base, will be removed from the REQUEST_URI
	 *
	 * @var string
	 */
	private $urlBase = '';

	/**
	 * Each of the URL parts in an array
	 *
	 * @var array
	 */
	private $urlParts = array();

	/**
	 * Copy of $this->urlParts to be used in ::get()
	 *
	 * @var array
	 */
	private $_urlParts = array();

	/**
	 * Just to make sure no one sends stupidly long url requests for this to process
	 */
	const EXPLODE_LIMIT = 15;

	/**
	 * Constructor
	 *
	 * @param string $urlBase
	 * @return object
	 */
	public function __construct($urlBase)
	{
		$this->urlBase = (string) $urlBase;

		// Add the leading / to the url base
		if($this->urlBase[0] !== '/')
			$this->urlBase = '/' . $this->urlBase;

		// Add the trailing / to the url base
		if(strrpos($this->urlBase, '/') !== (strlen($this->urlBase) - 1))
			$this->urlBase .= '/';

		$url = (string) $_SERVER['REQUEST_URI'];

		// remove the url base from the beginning
		if (strpos($url, $this->urlBase) === 0)
			$url = substr($url, strlen($this->urlBase) - 1);

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

			$this->urlParts[] = trim(htmlspecialchars(str_replace(array("\r\n", "\r", "\0"), array("\n", "\n", ''), $url[$i]), ENT_COMPAT, 'UTF-8'));

			// Var for all relative linking
			$this->webRootPath .= '../';
		}

		$this->_urlParts = $this->urlParts;
	}

	/**
	 * Build a URL
	 *
	 * @param array $urlAry Array of url chunks, "array('lol', 'asdf', '12345')" would create "/lol/asdf/12345"
	 * @param array $requestAry Array of request data ('id' => '123', 'mode' => 'edit')
	 * @param string $appendString A string that gets appended to the URL (for anchored links)
	 * @param bool $use_amp use &amp; (true) or & (false)
	 * @return string url
	 */
	public function build($urlAry = array(), $requestAry = array(), $appendString = '', $use_amp = true)
	{
		// Find the parts of the URL that are the same from the beginning.
		$congruences = 0;
		foreach($this->urlParts as $i => $element)
		{
			if($urlAry[$i] == $element)
				$congruences++;
		}

		// Get only the parts that are different
		$_url = array_slice($this->urlParts, $congruences);
		$urlAry = array_slice($urlAry, $congruences);

		// Create $url and start building the path
		$url = './';
		for($i = 0; $i < sizeof($_url); $i++)
			$url .= '../';

		// Add the new path
		$url .= implode('/', $urlAry) . (!empty($urlAry) ? '/' : '');

		// Add the _GET params
		if(sizeof($requestAry))
		{
			$url .= '?';
			$_requestAry = array();

			foreach($requestAry as $name => $value)
				$_requestAry[] = $name . '=' . $value;

			$glue = ($use_amp) ? '&amp;' : '&';
			$url .= implode($glue, $_requestAry);
		}

		// Anchor links
		if(!empty($appendString))
			$url .= $appendString;

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
		if (sizeof($this->_urlParts))
		{
			$return = array_shift($this->_urlParts);

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
		return sizeof($this->_urlParts) ? true : false;
	}
}
