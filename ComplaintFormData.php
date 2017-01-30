<?php
function sanitize($var){
	$var = trim($var);
	$var = htmlspecialchars($var);
	return $var;
}

class ComplaintFormData{
	public static $multiFields = ["plaintiff","defendant","witness","charge","date","time","location"];
	private $data = ["prefix"		=> -1,
					 "caseNumber"	=> -1,
					 "plaintiff" 	=> [],
					 "defendant" 	=> [],
					 "witness" 	 	=> [],
					 "charge"	 	=> [],
					 "sectionNumber"=> [],
					 "date"		 	=> [],
					 "time"		 	=> [],
					 "location"  	=> [],
					 "whatHappened" => []];
	
	public function addData($field, $entry){
		if(!array_key_exists($field, $this->data)){
			return false;
		}
		else{
			$entry = sanitize($entry);
			$this->data[$field][] = $entry;
			return true;
		}
	}
	
	public function getData($field){
		if(!array_key_exists($field, $this->data)){
			return false;
		}
		else{
			return $this->data[$field];
		}
	}

	function __construct(){
		if($_POST['newComplaint']){
		foreach(self::$multiFields as $field){
		  $num = 1;
		  while(isset($_POST[$field.'-'.$num])){
		    $this->addData($field,$_POST[$field.'-'.$num]);
		    $num++;
		  }
	    }
			
      }
	}
}
?>