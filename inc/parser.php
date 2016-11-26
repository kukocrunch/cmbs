<?php 
/** parser **/
namespace Includes\Parser;

class URI{

	public static function parseUri($uri){
		//var_dump(explode("/",$uri));
		return explode("/",$uri);
	}
}
?>