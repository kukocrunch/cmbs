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
//use Includes\Phpqrcode\QRencode as Qrcode;
use Config\Config;
use Model;
class main extends base {
	
	private $session;
	public function __construct(){
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		$config = new Config;
		$vars['title']	= "ATM Main Menu";
		$vars['config'] = $config;
		$vars['nonce'] = Nonce::generate();		
		echo $this->load->view( 'main.index', $vars );	
	}
	public function atm_qr( $vars ){
		session_start();
		Nonce::refresh();
		$config = new Config;
		$vars['title']	= "ATM Information";
		$vars['config'] = $config;
		$vars['nonce'] = Nonce::generate();	
		echo $this->load->view( 'default', $vars );	
		echo $this->load->view( 'tmps.qr' , $vars  );
		echo $this->load->view( 'main.atm' , $vars );
		//$vars['qrcode'] = new Qrcode();
	}

}
