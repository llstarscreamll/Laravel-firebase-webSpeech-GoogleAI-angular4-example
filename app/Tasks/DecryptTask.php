<?php

namespace App\Tasks;

/**
* DecryptTask Class.
*
* @author Johan Alvarez <llstarscreamll@hotmail.com>
*/
class DecryptTask
{
	private $key;
	private $iv;

	public function __construct()
	{
		$this->key = env('TOKEN_KEY');
		$this->iv =  env('TOKEN_IV');
	}

	/**
	 * Decrypts RIJNDAEL 256 token.
	 *
	 * @param  string $key key on .env file
	 * @param  string $iv iv on .env file
	 * @param  string $token token to decrypt
	 * @return string
	 */
	public function run($token)
	{
	    $key = hex2bin($this->key);
	    $iv = hex2bin($this->iv);
	    $token = hex2bin($token);

	    // decrypt token
	    $decoded = mcrypt_decrypt(
	    	MCRYPT_RIJNDAEL_128,
	    	$key,
	    	$token,
	    	MCRYPT_MODE_CBC,
	    	$iv
	    );

	    $decoded = $this->removePKCS7Padding($decoded);

	    return ($decoded);
	}

	/**
	 * Removes PKCS7 padding.
	 *
	 * @param  string
	 * @return string
	 */
	public function removePKCS7Padding($value)
	{
	    $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	    
	    // get last char ASCII value
	    $packing = ord($value[strlen($value) - 1]);

	    if ($packing && $packing < $blockSize) {
	        for ($P = strlen($value) - 1; $P >= strlen($value) - $packing; $P--) {
	            if (ord($value{$P}) != $packing) {
	                $packing = 0;
	            }
	        }
	    }

	    return substr($value, 0, strlen($value) - $packing); 
	}
}
