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
            $jsonResp['message'] = "METHOD NOT ALLOWED";
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


    public function regist( $vars ){
        filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $method = $_SERVER['REQUEST_METHOD'];
        $jsonResp = array();
        header("Content-Type: application/json");
        if($method != "POST"){
            $jsonResp['code'] = 405;
            $jsonResp['message'] = "METHOD NOT ALLOWED";
            print_r(json_encode($jsonResp));
            return false;
        } else{
            $fname = $_POST['fname'];
            $lname = $_POST['lname'];
            $email = $_POST['email'];
            $mobileNo = $_POST['mobileno'];
            extract($this->load->model('account'));
            $tempaccount = [
                "100509108930",
                "100063019269"
            ];
            $account->fname = $fname;
            $account->lname = $lname;
            $account->email_address = $email;
            $account->mobile_number = $mobileNo;
            $regist = $accountDAO->save($account);
            // print_r($regist);
            if( !is_numeric($regist) && !is_bool($regist) ){
                $jsonResp['code'] = "400";
                $jsonResp['message'] = "BAD REQUEST";
                print_r(json_encode($jsonResp));
                return false;
            }

            $jsonResp['code'] = "200";
            $jsonResp['message'] = "ACCEPTED";
            print_r(json_encode($jsonResp));
            return true;
        }

    }

    public function remit( $vars ){
        filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $method = $_SERVER['REQUEST_METHOD'];
        $jsonResp = array();
        header("Content-Type: application/json");
        if($method != "POST"){
            $jsonResp['code'] = 405;
            $jsonResp['message'] = "METHOD NOT ALLOWED";
            print_r(json_encode($jsonResp));
            return false;
        } else{
            $accountNo = $_POST['account_number'];
            $destNo = $_POST['destination_number'];
            $amount = $_POST['amount'];
            $curl = curl_init();
            $params = [
                "channel_id" => "BLUEMIX",
                "transaction_id" => rand(1000000000,9999999999),
                "source_account" => $accountNo,
                "source_currency" => "PHP",
                "target_account" => $destNo,
                "target_currency" => "PHP",
                "amount" => $amount
            ];
            curl_setopt_array($curl, array(
              CURLOPT_URL => "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/transfer",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($params),
              CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "content-type: application/json",
                "x-ibm-client-id: REPLACE_THIS_KEY",
                "x-ibm-client-secret: REPLACE_THIS_KEY"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              echo "cURL Error #:" . $err;
            } else {
              echo $response;
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
            
        }

    }

}
