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
 * OpenFlame Framework - Encrypt helper class,
 * 		Encryption helper class, decrypted encrypted exception data and helps easily generate OpenSSL keys.
 *
 *
 * @license     http://opensource.org/licenses/mit-license.php The MIT License
 * @link        https://github.com/emberlabs/openflame
 */
class EncryptHelper
{
	/**
	 * Generate an OpenSSL public/private keypair.
	 * @param string &$private_key - The generated private key.
	 * @param string &$public_key - The generated public key.
	 * @return true
	 *
	 * @throws \RuntimeException
	 */
	public static function createKeypair(&$private_key, &$public_key)
	{
		if(!function_exists('openssl_pkey_export'))
		{
			throw new \RuntimeException('Couldn\'t create keypair. Check that OpenSSL in PHP is configured properly.');
		}

		$res = openssl_pkey_new();

		if(!$res)
		{
			throw new \RuntimeException('Couldn\'t create keypair. Check that OpenSSL in PHP is configured properly - an openssl.cnf file is needed. Consult http://www.php.net/manual/en/openssl.installation.php');
		}

		$private_key = NULL;
		openssl_pkey_export($res, $private_key);

		$pubkey = openssl_pkey_get_details($res);
		$public_key = $pubkey["key"];

		return true;
	}

	/**
	 * Decrypt the data string provided using the specified private key.
	 * @param string $private_key - The private key to decrypt with.
	 * @param string $data - The data to decrypt.
	 * @return false|string - The decrypted string, or false if decryption failed (likely due to using an incorrect private key)
	 *
	 * @throws \RuntimeException
	 */
	public static function decryptData($private_key, $data)
	{
		if(!function_exists('openssl_private_decrypt'))
		{
			throw new \RuntimeException('Couldn\'t decrypt data. Check that OpenSSL in PHP is configured properly.');
		}

		$return = '';
		$result = openssl_private_decrypt($data, $return, $private_key);

		if($result === false)
		{
			return false;
		}

		return $return;
	}
}
