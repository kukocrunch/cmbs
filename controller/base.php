<?php

/** baseController **/
namespace Controller;
use Config\Config;
use Loader;
use Model;
abstract class base{
	
	protected $_registry;
	protected $loader;
	public function __construct(){
		$config = new Config;
		date_default_timezone_set("Asia/Tokyo");
		header('X-Frame-Options: SAMEORIGIN');
		$this->load = new Loader\load($config->view,$config->cache);
	}
	

	public function img_upload($vars){
		$config = new Config;
		$ds = DIRECTORY_SEPARATOR;
		$sf = $config->temp;
		extract($this->load->model("image"));
		if( !empty( $_FILES ) ){
			$tempf = $_FILES['file']['tmp_name'];
			$targetp = $config->uploads;
			$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
			$newfn = date("Ymd")."_".md5(time()).".".$ext; 
			$targetf = $targetp.$newfn;
			move_uploaded_file($tempf, $targetf);
			$image->image_name = $newfn;
			$image->image_type = $_FILES['file']['type'];
			$imageDAO->save($image);
			$imageSize = getimagesize($targetf);
			$returnvar = array(
					"filename" => $newfn,
					"width" => $imageSize[0],
					"height" => $imageSize[1]
				);
			echo json_encode($returnvar);
		}
	}
}