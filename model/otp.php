<?php
// User Model
namespace Model;
class Otp extends base {
    
    public $id;

    public $terminal_id;
    public $otp;
    public $account_number;
    public $mobile_number;
    public $amount;

    public $table = "otp";

    public function __construct(){
        parent::__construct();
    }


}

class OtpDAO extends baseDAO{

    //add addtional query functions here
    //add business logic here

    public function checkOtp($terminalId, $otp){
        return $this->select()
                  ->where('terminal_id',$terminalId)
                  ->where('otp',$otp)
                  ->grab(new Otp);
    }

}
