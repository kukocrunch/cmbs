<?php 
/*
	DO NOT PUT ANY BUSINESS LOGIC IN HERE! THAT GOES TO THE MODEL DAO CLASSES!
	CONTROLLERS ARE JUST FOR CONTROLLING NOT PROCESSING BUSSINESS LOGIC!
*/
//use Philo\Blade\Blade;
namespace Controller;
use Loader;
use Includes\Error;
use Includes\Common\Sanitizer as Sanitizer;
use Includes\Common\NonceGenerator as Nonce;
use Includes\Crypt\Encrypt as Encrypt;
use Includes\Crypt\Salt as Salter;
use Includes\Phpqrcode\QRcode as Qrcode;
use Includes\Curlinfo\Curl as BInfo;
use Config\Config;
use Model;
class generator extends base {
	
	private $session;
	public function __construct(){
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		$config = new Config;
		$branch_info = new BInfo;
		$term_id = $branch_info->get_info_Id(1015);
		//$info['TERMINAL_ID'] = Encrypt::rijndael( (string) $term_id[0]["id"]);
		//$info['ATM_NAME'] = Encrypt::rijndael( (string) $term_id[0]["name"] );
		$info['TERMINAL_ID'] = $term_id[0]["id"];
		$info['TERMINAL_NAME'] = $term_id[0]["name"];
		$info['TERMINAL_ADD'] = $term_id[0]["address"];
		$info['ATM_TM'] = date('Y-m-d H:i:s');
		$json = json_encode($info);
		$vars['qrcode'] = Qrcode::png($json);
	}

}
