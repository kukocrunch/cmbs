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
use Config\Config;
use Model;
class cmbsregistration extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		Nonce::generate();

		$config = new Config;
		$vars['title']	= "Register to CMBS";
		$vars['config'] = $config;
		$vars['nonce'] = $_SESSION["nonce"];
		
		echo $this->load->view( 'register.index', $vars );	
	}

	public function register(){
		if(!empty($_POST)){
			session_start();
			Sanitizer::sanitize_post();

			$config = new Config;

			extract($_POST);

			if( $nonce != $_SESSION["nonce"] ){
				header('Location:'. $config->http. '/?invalid_request');
			} else{
				extract( $this->load->model( "CmbsAccounts" ) );

				$CmbsAccounts->account_number = $account_number;
				$CmbsAccounts->mobile_number = $mobile_number;
				$CmbsAccounts->fname = $fname;
				$CmbsAccounts->lname = $lname;
				$CmbsAccounts->email_address = $email_address;
				$CmbsAccounts->timestamp = strtotime(date("Ymd"));
				$_id = $CmbsAccountsDAO->save( $CmbsAccounts );

				Nonce::refresh();
				Nonce::generate();

				$vars['config'] = $config;
				$vars['nonce'] = $_SESSION["nonce"];
				
				if($_id){
					$vars["title"] = "Registration Sucessful";

					echo $this->load->view( 'register.success', $vars );	
				} else{
					$vars["title"] = "Registration Error! Please try again.";
					$vars["error_flg"] = true;

					echo $this->load->view( 'register.index', $vars );
				}
			}
		} else
			header('Location:'.$config->http.'/?invalid_request');
	}
}
