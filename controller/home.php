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
class home extends base {
	
	private $session;
	public function __construct(){
		
		parent::__construct();
	}
	public function index( $vars ) {
		session_start();
		Nonce::refresh();
		Nonce::generate();
		$config = new Config;
		$vars['title']	= "Page title";
		$vars['config'] = $config;
		$vars['nonce'] = $_SESSION["nonce"];
		
		echo $this->load->view( 'home.index', $vars );	
	}

	public function transactions( $vars ){
		session_start();
		Nonce::refresh();
		Nonce::generate();
		$config = new Config;
		$vars['title']	= "Cardless transactions";
		$vars['config'] = $config;
		$vars['nonce'] = $_SESSION["nonce"];
		
		echo $this->load->view( 'home.transactions', $vars );	
	}

	public function logs( $vars ){
		session_start();
		Nonce::refresh();
		Nonce::generate();
		$config = new Config;
		$vars['title']	= "Transaction Logs";
		$vars['config'] = $config;
		$vars['nonce'] = $_SESSION["nonce"];

		extract($this->load->model( 'TransactionLogs' ));
		$transaction_logs = $TransactionLogsDAO->select()
									  		   ->grab( $TransactionLogs );

		$vars["transaction_logs"] = $transaction_logs;
		
		echo $this->load->view( 'home.transaction-logs', $vars );	
	}

	public function getTransactionLogs(){

	}

}
