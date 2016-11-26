<?php
/** loader **/
namespace Loader;

use Config\Config;
use Includes\Error;

	class load{

		public function model($name){
			$config = new Config;
			$modelPath  = $config->model.$name.'.php';
			if(is_readable($modelPath)){
				require_once($modelPath);
				//echo $modelPath;
				if(class_exists("Model\\".$name)){
					$_model = "Model\\".$name;
					$_dao = "Model\\".$name."DAO";
					$array[$name] = new $_model;
					$array[$name.'DAO'] = new $_dao;
					return $array;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}

		public function view($name,array $vars = null){
			$config = new Config;
			$path = explode( ".", $name );
			if( isset($path[0]) && $path[0]!="" ){
				$pathbuilder = $config->view;
				foreach($path as $filepath){
					$pathbuilder .=$filepath."/";
				}
				$pathbuilder = rtrim($pathbuilder,"/");
				$pathbuilder .=".html";
				if(is_readable($pathbuilder)){
					if(isset($vars)){
						$vars['page'] = $pathbuilder;
						extract($vars);
					}
					if(!isset($vars['frnt_flg'])){
		
						require($config->view."default.html");
					} else{
						
						require($pathbuilder);
					}
				}
			} else{
				new Error\MissingFile;
			}

		}

	}
?>