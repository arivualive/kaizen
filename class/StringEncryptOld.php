<?php

class StringEncryptOld{

	// ------------------------- private ---------------------------------------

	private $td;

	// ------------------------- public ----------------------------------------
	function __construct(){
		//暗号化＆復号化キー
		$key = md5( "thinkboard" );

		//暗号化モジュール使用開始
		$this->td  = mcrypt_module_open( 'des', '', 'ecb', '' );
		$key = substr( $key, 0, mcrypt_enc_get_key_size( $this->td ) );
		$iv  = mcrypt_create_iv( mcrypt_enc_get_iv_size( $this->td ), MCRYPT_RAND );
		
		//暗号化モジュール初期化
		if( mcrypt_generic_init( $this->td, $key, $iv ) < 0 ) {
		  exit( "error." );
		}
	}

	function encrypt( $str ){
		return base64_encode( mcrypt_generic( $this->td, base64_encode( $str ) ) );
	}

	function decrypt( $str ){
        if (! empty($str)) {
		    return base64_decode( mdecrypt_generic( $this->td, base64_decode( $str ) ) );
        }
	}

	function close(){
		mcrypt_generic_deinit($this->td);
		mcrypt_module_close($this->td);
	}
}

?>
