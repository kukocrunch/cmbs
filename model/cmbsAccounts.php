<?php
// User Model
namespace Model;
class CmbsAccounts extends base {
    
    public $id;

    public $account_number;

    public $mobile_number;

    public $fname;

    public $lname;

    public $email_address;

    public $timestamp;

    public $table = "cmbs_accounts";

    public function __construct(){
        parent::__construct();
    }


}

class CmbsAccountsDAO extends baseDAO{

    //add addtional query functions here
    //add business logic here

    public function checkCredentials($data = array()){
        if(!empty($data)){
            $result = $this->select()
                           ->orwhere('account_number', $data["account_number"])
                           ->orwhere('mobile_number', $data["mobile_number"])
                           ->orwhere('email_address', $data["email_address"])
                           ->grab(new CmbsAccounts);

        }
    }

}

?>