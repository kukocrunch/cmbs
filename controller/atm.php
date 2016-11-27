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
use Includes\Crypt\Salt as Salter;
use Includes\Curlinfo\Curl as BInfo;
use Config\Config;
use Controller\api as Api;
use Model;
class atm extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		$config = new Config;
		$vars['title']	= "Welcome to Bank";
		$vars['config'] = $config;
		$vars['frnt_flg'] = true;
		$vars['nonce'] = Nonce::generate();	
		$branch_info = new BInfo;	
		$term_id = $branch_info->get_info_Id(1015);
		$vars['TERMINAL_ID'] = $term_id[0]["id"];
		$vars['TERMINAL_NAME'] = $term_id[0]["name"];
		$vars['TERMINAL_ADD'] = $term_id[0]["address"];
		echo $this->load->view( 'default', $vars );	
		echo $this->load->view( 'atm.index', $vars );	
	}

	public function verify( $vars ){
		session_start();
		Nonce::refresh();
		$config = new Config;

		Sanitizer::sanitize_post();

		$branch_info = new BInfo;	
		$term_id = $branch_info->get_info_Id(1015);

		$vars['TERMINAL_ID'] = $term_id[0]["id"];
		$vars['title']	= "Verify Code";
		$vars['config'] = $config;
		$vars['nonce'] = $_SESSION["nonce"];

		extract( $this->load->model( 'otp' ));
		$otp = $otpDAO->checkOtp($term_id, $_POST["otp_key"]);

		if(!empty($otp)){
			$vars["type"] = "notif";
			$vars["mobile_number"] = $otp->mobile_number;
			$api = new Api();
			$result = $api->send($vars);

			$otpDAO->where('terminal_id',$term_id)
                   ->where('otp',$otp)
                   ->delete();

			echo $this->load->view( 'atm.banking_success', $vars );
		} else{
			echo $this->load->view( 'atm.banking_error', $vars );
		}
	}

}
