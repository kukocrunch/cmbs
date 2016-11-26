<?php
// User Model
namespace Model;
class Terminal extends base {
    
    public $id;

    public $terminal_id;

    public $otp;

    public $table = "atm_information";

    public function __construct(){
        parent::__construct();
    }


}

class TerminalDAO extends baseDAO{

    //add addtional query functions here
    //add business logic here

    public function getTerminal($terminalId){
        return $this->select()
                  ->where('terminal_id',$terminalId)
                  ->grab(new Terminal);
    }


    public function storeOtp($otp){
        $this->table = "otp";
        return $this->save($otp);
    }

    public function deleteOtp($otp){
        $this->table = "otp";
        return $this->delete($otp);
    }

    public function checkOtp($terminalId, $otp){
        $this->table = "otp";
        return $this->select()
                  ->where('terminal_id', $terminalId)
                  ->where('otp', $otp)
                  ->grab(new Terminal);
    }
}
