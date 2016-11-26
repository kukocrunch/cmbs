<?php
// User Model
namespace Model;
class TransactionLogs extends base {
    
    public $id;

    public $account_number;

    public $terminal_id;

    public $amount;

    public $transaction_type;

    public $timestamp;

    public $table = "transaction_logs";

    public function __construct(){
        parent::__construct();
    }


}

class TransactionLogsDAO extends baseDAO{

    //add addtional query functions here
    //add business logic here

}

?>