<?php

class StringEncrypt{

	// ------------------------- private ---------------------------------------

	private $method;
	private $key;

	// ------------------------- public ----------------------------------------
	function __construct(){
		$this->method = 'aes-128-ecb';

		$this->key = md5( "thinkboard" );
		$this->key = substr( $this->key, 0, 16 );
	}

	function encrypt( $plainText ){
		return openssl_encrypt( base64_encode($plainText), $this->method, $this->key );
	}

	function decrypt( $cipherText ){
        if (! empty($cipherText)) {
		    return base64_decode( openssl_decrypt($cipherText, $this->method, $this->key) );
        }
	}

	function close(){
	}
}

?>
