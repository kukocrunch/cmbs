<?php
// User Model
namespace Model;
class Account extends base {
    
    public $accountno;

    public $mobileno;

    public $table = "cmbs_accounts";

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
