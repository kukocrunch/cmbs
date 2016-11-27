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

}
