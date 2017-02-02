<?php
function sanitize($var){
	$var = trim($var);
	$var = htmlspecialchars($var);
	return $var;
}

$prefix = DATE('Y');

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
			if(is_array($this->data[$field])){
				$entry = sanitize($entry);
				$this->data[$field][] = $entry;
				return true;
			}
			else{
				$this->data[$field] = $entry;
			}
		}
	}
	
	public function getData($field, $as = "array/single"){
		if(!array_key_exists($field, $this->data)){
				return false;
		}
		else{
			if($as == "array/single")
				return $this->data[$field];
			else if($as == "string"){
				$returnStr = "";
				for($i = 0; $i < count($this->data[$field]); $i++){
					$returnStr = $returnStr.$this->data[$field][$i];
					if($i != count($this->data[$field]) - 1)
					  $returnStr = $returnStr.", ";
				}
				return $returnStr;
			}
		}
	}
	
	public function submitToDatabase(){
		$dbConn = new mysqli("localhost",'root');
		$dbConn->select_db("jcdb".$GLOBALS['prefix']);
		
		$string = "INSERT INTO casehistory(formScan,plaintiff,defendant,witness,dateOfIncident,timeOfIncident,location,charge,whatHappened) VALUES(";
		
		$date = date('Y-m-d',strtotime($this->getData('dateOfIncident','string')));
		
		$string = $string."'".$this->getData('formScan')."',";
		$string = $string."'".$this->getData('plaintiff','string')."',";
		$string = $string."'".$this->getData('defendant','string')."',";
		$string = $string."'".$this->getData('witness','string')."',";
		$string = $string."'".$date."',";
		$string = $string."'".$this->getData('timeOfIncident','string')."',";
		$string = $string."'".$this->getData('location','string')."',";
		$string = $string."'".$this->getData('charge','string')."',";
		$string = $string."'".$this->getData('whatHappened')."');";
	  
	    $dbConn->query($string);
		
		$sqlReturn = $dbConn->query("SELECT * FROM casehistory ORDER BY caseNumber DESC LIMIT 1;");
		
		$row = $sqlReturn->fetch_row();
		
		$sqlReturn->free();
		
		$this->addData("prefix",$row[0]);
		$this->addData("caseNumber",$row[1]);
	
		$dbConn->close();
		
		return $this->getData("prefix")."-".$this->getData("caseNumber");
	}

	function __construct($caseNum = null){		
		if($caseNum == null){
		  if($_POST['newComplaint']){
		    
			foreach(self::$multiFields as $field){
		    $num = 1;
		    while(isset($_POST[$field.'-'.$num])){
		      $this->addData($field,$_POST[$field.'-'.$num]);
		      $num++;
		  }
	    }
		
		$scanFileName = "./formScans".$GLOBALS['prefix']."/".Date('U').".jpg";
		if(!move_uploaded_file($_FILES['formScan']['tmp_name'],$scanFileName)){
			echo "FAIL!";
			echo $_FILES['formScan']['tmp_name'];
		}
		$this->addData("formScan", $scanFileName);	

		$this->addData("whatHappened",$_POST['whatHappened']);
	  }
     }
	}
}
?>