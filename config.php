<?php
/* config */

namespace Config;

class Config{


	public $http = "http://uhaccmbs/";
	public $https = "https://uhaccmbs/";
	public $dir_root = "";
	public $default = "home"; //default controller class
	public $url = "";
	public $controller = "";
	public $view = "";
	public $model = "";
	public $vendor = "";
	public $uploads = "";
	public $uploadUrl = "";
	public $cache = "";
	public $temp = "";
	public $common_css = "";
	public $common_img = "";
	public $common_js = "";
	public $common_assets = "";
	public $common_fonts = "";
	public $salt = "popcorn";
	private static $rdbms = "mysql";
	private static $host = "localhost";
	private static $port = "3306";
	private static $database = "uhaccmbs";
	public $dbuser = "root";
	public $dbpassword = "";
	public $vex = 0;


	public function __construct() {
		date_default_timezone_set("Asia/Manila");
		$this->dir_root = $_SERVER['DOCUMENT_ROOT'];
		$this->controller = $this->dir_root."/controller/";
		$this->view = $this->dir_root."/view/";
		$this->model = $this->dir_root."/model/";
		$this->cache = $this->dir_root."/cache/";
		$this->temp = $this->dir_root."/temp/";
		$this->vendor = $this->http."/vendor/";
		$this->uploads = $this->dir_root."/uploads/";
		$this->uploadUrl = $this->http."/uploads/";
		$this->common_css = $this->http."common/css/";
		$this->common_img = $this->http."common/img/";
		$this->common_js = $this->http."common/js/";
		$this->common_assets = $this->http."common/assets/";
		$this->common_fonts = $this->http."common/fonts/";
	}

	public static function connectTo(){
		$pdo = "";

		$pdo .=self::$rdbms.":";
		$pdo .=" host=".self::$host.";";
		$pdo .=" dbname=".self::$database."";
		//echo $pdo;
		return $pdo;
	}



}

?>