<?php 
/** crypt **/


/** You can add additional encryption processes here if you like **/
namespace Includes\Crypt;
use Config\Config;

class Encrypt{

	function rijndael( $string ) {
		$key = pack( 'H*', "42a308e942b04b437b26cb2905eba939" );   
		$iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC );
	    $iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );

		$text = mcrypt_encrypt( MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $iv );
	    $text = $iv . $text;
	    $output = base64_encode( $text );
	    return $output;
	}


}



class Decrypt{

	function rijndael( $string ){
		$key = pack( 'H*', "42a308e942b04b437b26cb2905eba939" );   
		$iv_size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC );
	    $iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );

	    if( strlen( $string ) > $iv_size ) {
	    	$text = base64_decode( $string );
		    $iv = substr( $text, 0, $iv_size );
		    $text = substr( $text, $iv_size );
		    $output = mcrypt_decrypt( MCRYPT_RIJNDAEL_128, $key, $text, MCRYPT_MODE_CBC, $iv );
	    } else {
	    	return 0;
	    }

	    return $output;
	}

}

class Salt{

	function SaltEmUp( $string ){
		$config = new Config;
		return hash("whirlpool",md5(md5($string).$config->salt),FALSE);
	}

}
?>