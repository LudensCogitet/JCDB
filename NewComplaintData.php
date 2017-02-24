<?php
require 'getDBIdent.php';

function sanitize($var){
	$var = trim($var);
	$var = htmlspecialchars($var);
	return $var;
}

$prefix = getDBIdent();

class NewComplaintData{
	public static $multiFields = ["plaintiff","defendant","witness","charge","dateOfIncident","timeOfIncident","location"];
	private $data = ["formScan"		  => "",
					 "prefix"		  => -1,
					 "caseNumber"	  => -1,
					 "plaintiff" 	  => [],
					 "defendant" 	  => [],
					 "witness"				=> [],
					 "charge"					=> [],
					 "sectionNumber"  => [],
					 "dateOfIncident" => [],
					 "timeOfIncident" => [],
					 "location"  	  => [],
					 "whatHappened"   => "",
					 "hearingDate"			=> [],
					 "hearingNotes"		=>	""];
	
	private $type;
	
	public function addData($field, $entry){
		if(!array_key_exists($field, $this->data)){
			return false;
		}
		else{
			if(is_array($this->data[$field])){
				$this->data[$field][] = sanitize($entry);
				return true;
			}
			else{
				$this->data[$field] = sanitize($entry);
				return true;
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
	
	private function makeNewDatabase($dbConn){
		$dbConn->query("CREATE DATABASE jcdb".$GLOBALS['prefix'].";");
		$dbConn->select_db("jcdb".$GLOBALS['prefix']);
		$dbConn->query("CREATE TABLE casehistory(prefix INTEGER DEFAULT ".$GLOBALS['prefix'].", caseNumber INTEGER AUTO_INCREMENT PRIMARY KEY, formScan TEXT, plaintiff TEXT, defendant TEXT, witness TEXT, dateOfIncident TEXT, timeOfIncident TEXT, location TEXT, charge TEXT, whatHappened TEXT, hearingDate TEXT, hearingNotes TEXT);");
		$dbConn->query("CREATE TABLE casestate(prefix INTEGER, caseNumber INTEGER, plaintiff TEXT, defendant TEXT, witness TEXT, charge TEXT, status TEXT, hearingDate TEXT, verdict TEXT, sentence TEXT, sentenceStatus TEXT, rowID INTEGER AUTO_INCREMENT PRIMARY KEY);");
	}
	
	public function submitToDatabase(){
		$dbConn = new mysqli("localhost",'root');
		
		if($this->type == "newComplaint"){
			if(!$dbConn->select_db("jcdb".$GLOBALS['prefix'])){
				$this->makeNewDatabase($dbConn);
			}
		
		$string = "INSERT INTO casehistory(formScan,plaintiff,defendant,witness,dateOfIncident,timeOfIncident,location,charge,whatHappened) VALUES (";
		
		$string = $string."'".$this->getData('formScan')."',";
		$string = $string."'".$this->getData('plaintiff','string')."',";
		$string = $string."'".$this->getData('defendant','string')."',";
		$string = $string."'".$this->getData('witness','string')."',";
		$string = $string."'".$this->getData('dateOfIncident','string')."',";
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
		
		$charges = $this->getData("charge");
		$defendants = $this->getData("defendant");
		
		$string = "INSERT INTO casestate(prefix,caseNumber,plaintiff,witness,status,charge,defendant) VALUES(".$this->getData('prefix').", ".$this->getData('caseNumber').", '".$this->getData('plaintiff','string')."', '".$this->getData('witness','string')."', 'pndg', ";
		
		foreach($charges as $charge){
			foreach($defendants as $defendant){
				$sendString = $string."'".$charge."', '".$defendant."');";
				$dbConn->query($sendString);
			}
		}
		$dbConn->close();
	  return "Case number: ".$this->getData("prefix")."-".$this->getData("caseNumber")." added to database.";
	 }
	 else if($this->type == "hearingNotes"){
		 $dbConn->select_db("jcdb".$this->getData('prefix'));
		 
		 $dbConn->query("UPDATE casehistory ".
										"SET hearingDate='".$this->getData("hearingDate","string")."', ".
										"hearingNotes='".$this->getData("hearingNotes")."'".
										"WHERE caseNumber=".$this->getData("caseNumber").";");
	 
	  $dbConn->close();
	  return "Hearing notes for case number: ".$this->getData("prefix")."-".$this->getData("caseNumber")." updated.";
	 }
	}

	function __construct(){		
		  if(isset($_POST['newComplaint'])){
		   $this->type = "newComplaint"; 
			foreach(self::$multiFields as $field){
		    $num = 1;
		    while(isset($_POST[$field.'-'.$num])){
		      $this->addData($field,$_POST[$field.'-'.$num]);
		      $num++;
		  }
		}
		
		$scanDirPath = "./formScans".$GLOBALS['prefix'];
		if(!file_exists($scanDirPath))
			mkdir($scanDirPath);
		
		$scanFileName = $scanDirPath."/".Date('U').".jpg";
		if(!move_uploaded_file($_FILES['formScan']['tmp_name'],$scanFileName)){
			echo "FAIL!";
			echo $_FILES['formScan']['tmp_name'];
		}
		$this->addData("formScan", $scanFileName);	

		$this->addData("whatHappened",$_POST['whatHappened']);
   }
	 else if(isset($_POST['newHearingNotes'])){
		$this->type = "hearingNotes";
		$num = 1;
		while(isset($_POST['hearingDate-'.$num])){
		  $this->addData('hearingDate',$_POST['hearingDate-'.$num]);
			$num++;
		}
		$this->addData('prefix',$_POST['prefix']);
		$this->addData('caseNumber',$_POST['caseNo']);
		$this->addData('hearingNotes',$_POST['hearingNotes']);
	 }
	}
}
?>