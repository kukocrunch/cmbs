<?php 
namespace Includes\Error;
use Config\Config;
class MissingController{

	public function __construct(){
		$config = new Config;
		require_once($config->dir_root."/404.php");
	}
}

class MissingFunction{

	public function __construct(){
		$config = new Config;
		require_once($config->dir_root."/404.php");
	}

}

class MissingFile{

	public function __construct(){
		$config = new Config;
		require_once($config->dir_root."/404.php");
	}

}

class NoRecordsFound{

	public static function message1($message="No Records Found!"){
		$config = new Config;
		return "Error : ".$message;

	}
}

?>