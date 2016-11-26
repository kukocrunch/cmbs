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
use Includes\Crypt\Encryption;
use Config\Config;
use Model;
class api extends base {
    
    private $session;
    public function __construct(){
        
        parent::__construct();
    }


    // public function index( $vars ){

    //     $vars["mobile_number"] = "639178826159";
    //     $vars["otp"] = "success";
    //     $this->send($vars);

    // }
    public function check( $vars ){

        // api/check/$accountid/$accountnumber/encrypted($terminal)
        filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $method = $_SERVER['REQUEST_METHOD'];
        $jsonResp = array();
        if($method !== 'GET'){
            header("Content-type: application/json");
            $jsonResp['code'] = 405;
            $jsonResp['message'] = "Method not Allowed";
        } else{
            //check in if account exists
            $accountNo = $vars[0];
            $mobileNo = $vars[1];
            $terminalNo = $vars[2];
            header('Content-type: application/json');
            extract($this->load->model('account'));
            extract($this->load->model('terminal'));
            $account = $accountDAO->getAccount($accountNo, $mobileNo);
            $terminal = $terminalDAO->getTerminal($terminalNo);
            if(!empty($account) && !empty($terminal)){
                $vars['type'] = "success";
                $vars['mobile_number'] = $mobileNo;
                $vars['terminalNo'] = $terminalNo;
                $this->send($vars);
            } else{
                $vars['otp'] = "fail";
                $this->send($vars);
            }

        }
    }

    public function send( $vars ){
        // api/send/otp/$mobilenumber
        // api/check/$accountid/$accountnumber/encrypted($terminal)/
        $type = $vars['type'];
        $config = new Config();
        Nonce::generate();
        $url = $config->chikka;
        $otp = 0;
        $params = [
            "message_type" => "SEND",
            "mobile_number" => $vars['mobile_number'],
            "shortcode" => $config->chikka_sc,
            "message_id" => rand(1000000000,9999999999),
            "client_id" => $config->chikka_ci,
            "secret_key" => $config->chikka_sk
        ];
        switch($type){
            case "success":
                $randpin = rand(100000,999999);
                $params['message'] = "Thank you for choosing CMBS. Your one time use pin is: ".$randpin;
                break;
            case "fail":
                $params['message'] = "Thank you for choosing CMBS. Unfortunately your account does not exist. Please try again.";
                break;
            case "notif":
                $params['message'] = "Your transaction has been complete.";
                break;
            default:
                print_r(json_encode(array("code"=>"400","message"=>"Bad Request")));
                return false;
                break;

        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => http_build_query($params),
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/json"
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        print_r($response);
        print_r($err);
         if($response){
            $otp = $TerminalDAO->checkOtp($vars["terminalNo"], $randpin);
            if(empty($otp)){
                extract( $this->load->model( 'Terminal' ));

                $Terminal->terminal_id = $vars["terminalNo"];
                $Terminal->otp = $randpin;

                $terminal_id = $TerminalDAO->storeOtp( $Terminal );
            }
        }
    }

}
