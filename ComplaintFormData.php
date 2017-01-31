<?php
function sanitize($var){
	$var = trim($var);
	$var = htmlspecialchars($var);
	return $var;
}

$prefix = Date('Y');
$dbConn = new mysqli($_SERVER['SERVER_ADDR'],"root");

class ComplaintFormData{
	public static $multiFields = ["plaintiff","defendant","witness","charge","dateOfIncident","timeOfIncident","location"];
	private $data = ["formScan"		  => "",
					 "prefix"		  => -1,
					 "caseNumber"	  => -1,
					 "plaintiff" 	  => [],
					 "defendant" 	  => [],
					 "witness" 	 	  => [],
					 "charge"	 	  => [],
					 "sectionNumber"  => [],
					 "dateOfIncident" => [],
					 "timeOfIncident" => [],
					 "location"  	  => [],
					 "whatHappened"   => ""];
	
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

	function __construct($caseNum = null){
		global $prefix;
		global $dbConn;
		
		if($caseNum == null){
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
}
?>