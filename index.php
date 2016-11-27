<?php
date_default_timezone_set( "Asia/Tokyo" );
require_once( $_SERVER['DOCUMENT_ROOT']."/config.php" );
//require __DIR__ . '/vendor/autoload.php';
//require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Config\Config;
$config = new Config;
foreach( glob($config->dir_root."/inc/*php" ) as $filename ){
	include $filename;
}
foreach( glob( $config->dir_root."/app/*.php" ) as $filename ){
	require_once($filename);
}


use Includes\Parser\URI as uriparser;


$uriArray = uriparser::parseUri( trim($_SERVER['REQUEST_URI'],"/") );
require_once( $config->controller."base.php" );
require_once( $config->model."base.php" );


if( isset($_GET['class']) && $_GET['class'] !='' ){
	try{
		if( file_exists( $config->controller.trim($_GET['class']).".php" ) ){
			require_once( $config->controller.trim($_GET['class']).".php" );
			$control = "Controller\\".trim( $_GET['class'] );
			$controller = new $control;
						
			$function = "index";

			if( isset( $_GET['function'] ) && $_GET['function'] != ""){
				$function = trim( $_GET['function'] );
			}
			
			$vars = array();
			$uri_size = sizeof($uriArray);

			foreach($uriArray as $k => $v){
				foreach($_GET as $key => $var){
					if($v != $var){
						$vars[] = $v;
					}
				}
			}
			try{
				if(is_callable(array("Controller\\".trim($_GET['class']),$function))) { 
					call_user_func_array(array($controller,$function),array($vars));
				} else {
					new Includes\Error\MissingFunction;
				}
			} catch(Exception $e){
				echo $e;
			}
			
		} else{
			new Includes\Error\MissingController;
		}

	} catch(Exception $e){
		echo $e;
	}

} else if( isset($_GET['class']) && $_GET['class'] =='' ){
	try{

		if( file_exists( $config->controller.$config->default.".php" ) ){
			require_once( $config->controller.$config->default.".php" );
			
			$control = "Controller\\".$config->default;
			$controller = new $control;
			$vars = array();
			foreach($uriArray as $k => $v){
				foreach($_GET as $key => $var){
					if($v != $var){
						$vars[] = $v;
					}
				}
			}
			call_user_func_array( array( $controller,"index" ), array( $vars ) );
		} else{
			new Includes\Error\MissingController;
		}

	} catch(Exception $e){
		echo $e;
	}
} 



?>