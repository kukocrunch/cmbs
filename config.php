<?php
/* config */

namespace Config;

class Config{
	public $http = "http://cmbs.dev:81/";
	public $https = "https://cmbs.dev:443/";
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
	public $chikka = "https://post.chikka.com/smsapi/request?";
	public $chikka_ci = "5460bb2e9a263b5b7d0e908bc49772f6cfa6ba0b32e9f010a03b8e9db7211a76";
	public $chikka_sk = "a2cb2102075564aba2469dbca8b2d5ee8d2eb3ee8e7ef2457774a3b4c70a0fa7";
	public $chikka_sc = "292909993";
	public $ubank_ci = "81420b44-7b5d-4291-8109-0c69a3d989f6";
	public $ubank_sk = "E6cE0oU3sE2gB4yW3mB6iG6fI2kH8yF8oJ4cS2hB8bE8eR1gU1";
	public $salt = "popcorn";
	public $w_min = "500";
	public $w_max = "10000";
	private static $rdbms = "mysql";
	// private static $host = "us-cdbr-iron-east-04.cleardb.net";
	// private static $port = "3306";
	// private static $database = "ad_fa050ab29d9f1cd";
	// public $dbuser = "b269cef52a54a2";
	// public $dbpassword = "2a5a1cfd";
	private static $host = "localhost";
	private static $port = "3306";
	private static $database = "cws";
	public $dbuser = "root";
	public $dbpassword = "";
	public $vex = 0;
	//ben - api key
	public static $_client_id = "e8ca76af-5687-4c07-a5bf-d6004571366d";
	public static $_client_secret = "oX7xB2jS1eB6kE5mL0nN2cF0hJ4rX7mX4dI1gO0uT3pG1jA3pL";


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