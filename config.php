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
	public $chikka_ci = "30c95e4df6c9bf76fef37d6415bcdb243da982ca32c04df4d70190ca8f82abbf";
	public $chikka_sk = "1c539a8b5a540a6f3d2fbac6ea828fc967d477a6b10747a2bd23d90cc272ae88";
	public $chikka_sc = "29290396329";
	public $chikka = "https://post.chikka.com/smsapi/request?";
	public $salt = "popcorn";
	private static $rdbms = "mysql";
	private static $host = "localhost";
	private static $port = "3306";
	private static $database = "cws";
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
		$pdo .="host=".self::$host.";";
		$pdo .=" port=".self::$host.";";
		$pdo .=" dbname=".self::$database."";
		//echo $pdo;
		return $pdo;
	}



}

?>