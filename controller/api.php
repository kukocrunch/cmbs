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
                $vars['otp'] = "success";
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
        $otp = $vars['otp'];
        $config = new Config();
        Nonce::generate();
        $url = $config->chikka;
        $randpin = 0;
        $params = [
            "message_type" => "SEND",
            "mobile_number" => $vars['mobile_number'],
            "shortcode" => $config->chikka_sc,
            "message_id" => rand(1000000000,9999999999),
            "client_id" => $config->chikka_ci,
            "secret_key" => $config->chikka_sk
        ];
        switch($otp){
            case "success":
                $randpin = rand(100000,999999);
                $params['message'] = "Thank you for choosing CMBS. Your one time use pin is: ".$randpin;
                break;
            case "fail":
                $params['message'] = "Thank you for choosing CMBS. Unfortunately your account does not exist. Please try again.";
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



    private function sendTransfer( $vars ){
        $curl = curl_init();
        $config = new Config;
        $channel_id = "UHACK_0028";
        extract( $this->load->model( "transaction" ) );
        extract( $this->load->model( "account" ) );
        $account_id_from = $_SESSION['account_id'];
        $account_id_to = $accountDAO->getByMobileNo($vars['mobile_no'])->id;
        $transaction->type = "stok";
        $transaction->dest_mobile = $vars['mobile_no'];
        $transaction->source_mobile = $accountDAO->getByAccountId($account_id_from)->mobile_no;
        $transaction->amount = $vars['amount'];
        // var_dump($transaction);
        $transaction_id = $transactionDAO->save($transaction, true);
        
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/transfer",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_SSL_VERIFYHOST => 0,
          CURLOPT_SSL_VERIFYPEER => 0,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => "{\"channel_id\":\"".$channel_id."\",\"transaction_id\":\"".$transaction_id."\",\"source_account\":\"".$_SESSION['account_id']."\",\"source_currency\":\"php\",\"target_account\":\"".$account_id_to."\",\"target_currency\":\"php\",\"amount\":".$vars['amount']."}",
          CURLOPT_HTTPHEADER => array(
            "accept: application/json",
            "content-type: application/json",
            "x-ibm-client-id: bfac49db-0569-412d-925b-263b3e640c4c",
            "x-ibm-client-secret: uU0vR1hC1bT6xI3cP1jD3uI1jW6cK0nG0iS4fT6vO8mL2xL6kJ"
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
          echo "cURL Error #:" . $err;
        } else {
          $response = json_decode($response);
          $transaction->id = "";
          if( $response->status != "S" || $response->error_message != ""){
            // header("Location:".$config->http."funds/transfer/?error=".$response->error_message);
          } else{
                $transaction->reference = $response->confirmation_no;
                
                $update = $transactionDAO->updateTransaction( $transaction, $transaction_id );
                $transaction = $transactionDAO->getTransactionById($transaction_id);
                
                Nonce::refresh();
                $config = new Config;
                $vars['title']  = "Funds - Transfer";
                $vars['config'] = $config;
                $vars['nonce'] = Nonce::generate();
                $vars['account'] = $accountDAO->getByMobileNo($vars['mobile_no']);
                echo $this->load->view( 'funds.transfer', $vars );  
          }
          
        }
    }

    public function generate( $vars ){
        //
    }

    public function transact( $vars ){
        // api/transact/{withdraw,deposit,pay}/accountid
    }



}
