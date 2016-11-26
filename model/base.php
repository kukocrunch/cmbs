<?php 
/** Base Model **/ 
namespace Model;
use Config\Config;
use PDO;
abstract class base {
	
	public $table = "";

	public function __construct() {
		//do stuff
		//you can add other construct processes here like additional properties and stuff
	}
	
}

abstract class baseDAO{

	public $dbh;
	
	public function __construct() {
		$config = new Config;
		$this->dbh = new PDO($config->connectTo(), $config->dbuser, $config->dbpassword);
	}

	public $where = false;
	public $fields = array();
	public $select = "";

	public $conditions = array();
	public $values = array();
	
	public $order = "";
	public $limit = "";
	public $offset = "";
	
	public $orwhere = false;
	
	public $orfields = array();
	public $orconditions = array();
	public $orvalues = array();

	public $join = "";

	public $distinct = false;
	
	public $count = false;

	public $query;

	public function closeConnection(){
		$this->dbh = null;
		return $this;
	}


	public function remove_unwanted($array){
		$not_allowed = [
			'table',
			'dbh'
		];

		$filtered_array = array_filter(
				$array,
				function ( $key ) use ( $not_allowed ){
					return !in_array( $key, $not_allowed );
				},
				ARRAY_FILTER_USE_KEY
			);

		return $filtered_array;

	}

	public function flush(){
		$this->where = false;
		$this->fields = array();
		$this->select = "";
		$this->conditions = array();
		$this->values = array();
		$this->order = "";
		$this->limit = "";
		$this->offset = "";
		$this->orwhere = false;
		$this->orfields = array();
		$this->orconditions = array();
		$this->orvalues = array();
		$this->join = "";

		return $this;
	}
	public function distinct(){
		$this->distinct = true;
		return $this;
	}

	public function fields(){
		$args = func_get_args();
		foreach($args as $arg){
			$this->fields[] = $arg;
		}
		return $this;
	}

	public function count(){
		$this->count = true;
		return $this;
	}

	public function select(){
		$args = func_get_args();

		if(sizeof($args) == 1 && $args[0] == "*"){
			$this->select = "*";
		} else{
			foreach($args as $arg){
				$this->select .= $arg.",";	
			}
			$this->select = rtrim( $this->select, ",");
			
		}
		return $this;
	}
	public function values(){
		$args = func_get_args();
		foreach($args as $arg){
			$this->values[] = $arg;
		}
		return $this;
	}

	public function where(){
		$args = func_get_args();
		if(sizeof($args) == 2){
			$this->fields[] = $args[0];
			$this->conditions[] = "=";
			$this->values[] = $args[1];
			$this->where = true;
			return $this;
		} else if(sizeof($args) == 3){
			$this->fields[] = $args[0];
			$this->conditions[] = $args[1];
			$this->values[] = $args[2];
			$this->where = true;
			return $this;
		} else{
			return "Inconsistent WHERE parameters.";
		}
	}

	public function orwhere(){
		$args = func_get_args();
		if(sizeof($args) < 3 && sizeof($args) > 1){
			$this->orfields[] = $args[0];
			$this->orconditions[] = "=";
			$this->orvalues[] = $args[1];
			$this->orwhere = true;
			return $this;
		} else if(sizeof($args) == 3){
			$this->orfields[] = $args[0];
			$this->orconditions[] = $args[1];
			$this->orvalues[] = $args[2];
			$this->orwhere = true;
			return $this;
		} else{
			return "Inconsistent WHERE parameters.";
		}
	}

	public function join($table1,$table2,$column=null,$condition,$joinin="INNER"){
		if(isset($table1) && isset($table2)){
			$this->join .=" ".$joinin." JOIN ".$table2." ON ".$table1.".".$column." ".$condition." ".$table2.".".$column." ";
		}
		return $this;
	}

	public function order(){
		$args = func_get_args();
		if(isset($args[0])){
			$this->order .= "ORDER BY ".$args[0];
			if(isset($args[1]) && (strtolower($args[1]) == "asc" || strtolower($args[1]) == "desc")){
				$this->order .=" ".$args[1];
			}
		}
		return $this;
	}

	public function offset(){
		$args = func_get_args();
		if(isset($args[0])){
			$this->offset = $args[0];
		}
					
		return $this;
	}

	public function limit(){
		$args = func_get_args();
		if(isset($args[0])){
			$this->limit = $args[0];
		}
				
		return $this;
	}
	public function save($model, $getlast = false) {
		$val = array();
		//var_dump(get_object_vars($model));
		foreach(get_object_vars($model) as $key => $value){
			if($key !== 'table' && $key != $model->table."_id" && $key!='dbh' && $key != 'conn' && $value != '' && $value != null){
				$this->fields[] = $key;
				$this->values[] = $value; 
				$val[] = "?";
			}				
		}
		
		$stmt = "INSERT INTO ".$model->table."(".implode(",",$this->fields).") VALUES(".implode(",",$val).")";
		//var_dump($this->fields);
		$query = $this->dbh->prepare($stmt);
		$a = $query->execute($this->values);
		if($a){
			$this->flush();
			if( $getlast = true ){
				return $this->dbh->lastInsertId();
			} else{
				return true;
			}
		} else{
			$this->flush();
			 var_dump($query->errorInfo());
		}
	}

	public function delete($model){

		$stmt = "DELETE FROM ".$model->table;
		if($this->where){
			$stmt .=" WHERE ";
			$values = array();
			for($i = 0;$i<sizeof($this->fields);$i++){
				if($i!=0){
					$stmt .=" AND ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				} else{
					$stmt .=" ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				}
				$values[] = $this->values[$i];
			}
			if($this->orwhere){
				$stmt .=" OR (WHERE ";
				for($i = 0;$i<sizeof($this->orfields);$i++){
					if($i!=0){
						$stmt .=" AND ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					} else{
						$stmt .=" ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					}
				}
				$stmt .=") ";
				$values[] = $this->orvalues[$i];
			}
			$query = $this->dbh->prepare($stmt);
			$this->flush();
			return $query->execute($values);

		}
		
	}
	
	public function update($model) {
		$stmt = "UPDATE ".$model->table." SET ";
		$values = array();
		$updatfields = array();

		foreach(get_object_vars($model) as $key=>$value){
			if($value != "" && $key !== 'table' && $key != $model->table."_id" && $key!='dbh' && $key != 'conn'){
				$updatefields[] = $key."= ?";

				$values[] = $value;
			}
		}
		echo "<br/>";
		$stmt .= " ".implode(",",$updatefields)." ";
		echo "<br/>";
		if($this->where){
			// var_dump($this->fields[$i])."<br/>";
			$stmt .=" WHERE ";
			for($i = 0;$i<sizeof($this->fields);$i++){
				if($i!=0){

					$stmt .=" AND ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				} else{
					$stmt .=" ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				}
				$values[] = $this->values[$i];
			}
			if($this->orwhere){
				$stmt .=" OR (WHERE ";
				for($i = 0;$i<sizeof($this->orfields);$i++){
					if($i!=0){
						$stmt .=" AND ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					} else{
						$stmt .=" ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					}
				}
				$stmt .=") ";
				$values[] = $this->orvalues[$i];
			}
		}
		// var_dump($values);
		$this->dbh->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING );
		$query = $this->dbh->prepare($stmt);
		
		$this->flush();
		// return $query->execute($values);
		if (!$query->execute($values)) {
		    echo "\nPDO::errorInfo():\n";
		    print_r($this->dbh->errorInfo());
		} else{
			return true;
		}
	}

	public function grab($model) {
		
		$args = func_get_args();
		
		$stmt = "SELECT ";
		if( $this->select) {
			if($this->distinct){
				$stmt .="DISTINCT ";
			}
			$stmt .= " ".$this->select." FROM ".$model->table."  ";
 			
		} else {
			$stmt .= " * FROM ".$model->table." ";
		}


		$values = array();

		if($this->join != ""){
			$stmt .= $this->join." ";
		}

		if($this->where){
			$stmt .=" WHERE ";
			$values = array();

			for($i = 0;$i<sizeof($this->fields);$i++){
				if($i!=0){
					$stmt .=" AND ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				} else{
					$stmt .=" ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				}
				$values[] = $this->values[$i];
			}
			if($this->orwhere){
				$stmt .=" OR ( ";
				for($i = 0;$i<sizeof($this->orfields);$i++){
					if($i!=0){
						$stmt .=" AND ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					} else{
						$stmt .=" ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					}
					$values[] = $this->orvalues[$i];
				}
				$stmt .=") ";
				
			}
		}

		if($this->order!=""){
			$stmt .= " ".$this->order;
		}

		
		if($this->limit !=""){
			$stmt .="  LIMIT ".$this->limit;
		}
		if($this->offset!=""){
			$stmt .=" OFFSET ".$this->offset;
		}
		
		$query = $this->dbh->prepare($stmt);
		$this->query = $stmt;
		$query->execute($values);
		
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$convertedresults = array();

		foreach ($results as $key => $result) {
			$resultModel = get_class($model);

			$resultObject = new $resultModel;
			foreach($result as $key => $value){
				$resultObject->$key = $value;
			}
			$resultObject->table = "";
			$resultArray = ( array ) $resultObject;
			if($this->select !="" && $this->select !="*"){
				foreach(  $resultArray as $key => $val){
					if(!in_array($key, explode(",",$this->select) ) ){
						unset($resultArray[$key]);
					} else{
						$resultArray[$key] = $val;
					}
				} 
			}
			$convertedresults[]= (object) $resultArray;

		}
		$this->flush();
		if( count($convertedresults) == 1 ){
			$convertedresults = $convertedresults[0];
		}
		return $convertedresults;
	}

	public function grab_row($model) {
		
		$args = func_get_args();
			
		$stmt = "SELECT ";
		if( $this->select) {
			if($this->distinct){
				$stmt .="DISTINCT ";
			}
			$stmt .= " ".$this->select." FROM ".$model->table."  ";
 			
		} else {
			$stmt .= " * FROM ".$model->table." ";
		}


		$values = array();

		if($this->join != ""){
			$stmt .= $this->join." ";
		}

		if($this->where){
			$stmt .=" WHERE ";
			$values = array();

			for($i = 0;$i<sizeof($this->fields);$i++){
				if($i!=0){
					$stmt .=" AND ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				} else{
					$stmt .=" ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				}
				$values[] = $this->values[$i];
			}
			if($this->orwhere){
				$stmt .=" OR ( ";
				for($i = 0;$i<sizeof($this->orfields);$i++){
					if($i!=0){
						$stmt .=" AND ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					} else{
						$stmt .=" ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					}
					$values[] = $this->orvalues[$i];
				}
				$stmt .=") ";
				
			}
		}

		if($this->order!=""){
			$stmt .=" ".$this->order;
		}
		$query = $this->dbh->prepare($stmt);
		$this->query = $stmt;
		$query->execute($values);
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$row = null;
		foreach ($results as $key => $result) {
			$resultModel = get_class($model);

			$resultObject = new $resultModel;
			foreach($result as $key => $value){
				$resultObject->$key = $value;
			}
			$resultObject->table = "";
			$resultArray = ( array ) $resultObject;
			if($this->select !=""){
				foreach(  $resultArray as $key => $val){
					if(!in_array($key, explode(",",$this->select) ) ){
						unset($resultArray[$key]);
					} else{
						$resultArray[$key] = $val;
					}
				} 
			}
			$row = (object) $resultArray;

		}
		$this->flush();
		return (object) array_filter( ( array ) $row );
	}

	public function grab_count($model,$num=null) {
		
		$args = func_get_args();
			
		$stmt = "SELECT COUNT(*) as count ";
		$stmt .= "FROM ".$model->table." ";



		$values = array();

		if($this->join != ""){
			$stmt .= $this->join." ";
		}

		if($this->where){
			$stmt .=" WHERE ";
			$values = array();

			for($i = 0;$i<sizeof($this->fields);$i++){
				if($i!=0){
					$stmt .=" AND ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				} else{
					$stmt .=" ".$this->fields[$i]." ".$this->conditions[$i]." ?";
				}
				$values[] = $this->values[$i];
			}
			if($this->orwhere){
				$stmt .=" OR ( ";
				for($i = 0;$i<sizeof($this->orfields);$i++){
					if($i!=0){
						$stmt .=" AND ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					} else{
						$stmt .=" ".$this->orfields[$i]." ".$this->orconditions[$i]." ?";
					}
					$values[] = $this->orvalues[$i];
				}
				$stmt .=") ";
				
			}
		}

		if($this->order!=""){
			$stmt .=" ".$this->order;
		}
		$query = $this->dbh->prepare($stmt);
		$query->execute($values);
		$results = $query->fetch(PDO::FETCH_NUM);
		$this->flush();
		return $results[0];
	}

}
?>