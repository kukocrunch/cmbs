<?php
// User Model
namespace Model;
class Account extends base {

    public $table = "cmbs_accounts";

    public $id = "";

    public $account_number = "";

    public $fname = "";

    public $lname = "";

    public $mobile_number = "";

    public $email_address = "";
    
    public function __construct(){
        parent::__construct();
    }


}

class AccountDAO extends baseDAO{

    //add addtional query functions here
    //add business logic here

    public function getAccount( $account, $mobile ){
        return $this->select()
                   ->where('account_number', $account)
                   ->where('mobile_number', $mobile)
                   ->grab(new Account);

    }

}
