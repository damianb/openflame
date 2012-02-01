<?php
/**
 *
 * @package     openflame-framework
 * @subpackage  exception
 * @copyright   (c) 2010 - 2012 emberlabs.org
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 *
 * Minimum Requirement: PHP 5.3.0
 */

namespace emberlabs\openflame\Exception;
use \emberlabs\openflame\Core;

/**
 * OpenFlame Framework - Encrypted exception handler class,
 * 		Extension handler class, builds a page that shows encrypted debug information to prevent eavesdropping.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class EncryptedHandler extends Handler
{
	/**
	 * @var array - Array of options for the handler.
	 */
	protected static $options = array(
		'format'		=> NULL,
		'debug'			=> false,
		'unwrap'		=> 0,
		'closure'		=> NULL,
		'context'		=> true,
		'publickey'		=> NULL,
	);

	/**
	 * @var string - The public key to use for encryption.
	 */
	protected $public_key = NULL;

	/**
	 * Register the exception handler.
	 * @return void
	 */
	public static function register()
	{
		set_exception_handler('\\emberlabs\\openflame\\Exception\\EncryptedHandler::catcher');
	}

	/**
	 * Create the exception handler instance and prepare to handle the page
	 * @param \Exception $e - The exception we're handling.
	 * @param array $options - The array of options to pass to the exception handler instance.
	 *
	 * @note: Script execution terminates at the end of the constructor.
	 */
	protected function __construct(\Exception $e, array $options)
	{
		$this->public_key = $options['publickey'];

		parent::__construct($e, $options);
	}

	/**
	 * Set the public key to use to encrypt all exception data with (unless in debug mode, in which case it's just handed right out anyways)
	 * @param string $public_key - The public key string or path to the public key file to use.
	 * @return void
	 */
	final public static function setPublicKey($public_key)
	{
		if(!is_file($public_key))
		{
			static::$options['publickey'] = $public_key;
		}
		else
		{
			static::$options['publickey'] = file_get_contents($public_key);
		}
	}

	/**
	 * Get the public key in use for encrypting exception data with.
	 * @return string - The public key in use.
	 */
	final public static function getPublicKey()
	{
		return static::$options['publickey'];
	}

	/**
	 * Display a non-technical and obscure error message.
	 * @param string $page_format - The page format to use for this page.
	 * @return string - The HTML to display.
	 */
	protected function badassError($page_format)
	{
		if(!$this->public_key || !function_exists('openssl_public_encrypt'))
		{
			return parent::badassError($page_format);
		}

		// Stuff all the data to encrypt into $data for json encoding and encryption
		$e = $this->exception;
		$data = array(
			'exception'		=> array(
				'type'			=> get_class($e),
				'message'		=> $e->getMessage(),
				'code'			=> $e->getCode(),
				'context'		=> implode('', $this->traceException($e->getFile(), $e->getLine(), 7)),
				'file'			=> $e->getFile(),
				'line'			=> $e->getLine(),
				//'trace'			=> $e->getTrace(),
			),
			'super'			=> array(
				'server'		=> $_SERVER,
				'request'		=> $_REQUEST,
				'env'			=> $_ENV,
			),
		);

		if(static::$options['context'])
		{
			$data['exception']['trace'] = $this->highlightTrace(implode('', $this->traceException($this->exception->getFile(), $this->exception->getLine(), 7)));
		}

		$e_encrypted_string = '';
		$json = \emberlabs\openflame\Utility\JSON::encode($data);
		$res = openssl_public_encrypt($json, $e_encrypted_string, $this->public_key);

		$message = <<<EOD
						<div>
							Uh oh!  We appear to have encountered an error during your request.  Please report the error below to a site administrator.<br /><br />
							Error data: <br />
							<div style="background: #ffffff; color: #007700; padding: 0 3px; border: solid 1px #007700;"><pre style="font-family: 'Droid Sans Mono', monospace;">{$e_encrypted_string}</pre></div>
						</div>
EOD;
		return $this->buildHTML($page_format, 'Unexpected Exception', $message);
	}
}
