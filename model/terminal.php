<?php
// User Model
namespace Model;
class Terminal extends base {
    
    public $id;

    public $terminal_id;

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

}
