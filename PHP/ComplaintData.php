<?php
require './getDBIdent.php';

function sanitize($var){
	$var = trim($var);
	$var = htmlspecialchars($var);
	return $var;
}

$DBPrefix = getDBIdent();

class ComplaintData{
	public static $multiFields = ["plaintiff","defendant","witness","charge","dateOfIncident","timeOfIncident","location","hearingDate"];
	public static $otherFields = ["whatHappened","hearingNotes"];
	private $data = ["formScan"		  => "",	
					 "prefix"	=> -1,
					 "caseNumber"	  => -1,
					 "plaintiff" 	  => [],
					 "defendant" 	  => [],
					 "witness"				=> [],
					 "charge"					=> [],
					 "dateOfIncident" => [],
					 "timeOfIncident" => [],
					 "location"  	  => [],
					 "whatHappened"   => "",
					 "hearingDate"			=> [],
					 "hearingNotes"		=>	""];
	
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
			if($as == "array/single" || !is_array($this->data[$field]))
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
		$dbConn->query("CREATE DATABASE jcdb".$GLOBALS['DBPrefix'].";");
		$dbConn->select_db("jcdb".$GLOBALS['DBPrefix']);
		$dbConn->query("CREATE TABLE casehistory(prefix INTEGER DEFAULT ".$GLOBALS['DBPrefix'].", caseNumber INTEGER AUTO_INCREMENT PRIMARY KEY, formScan TEXT, plaintiff TEXT, defendant TEXT, witness TEXT, dateOfIncident TEXT, timeOfIncident TEXT, location TEXT, charge TEXT, whatHappened TEXT, hearingDate TEXT, hearingNotes TEXT);");
		$dbConn->query("CREATE TABLE casestate(prefix INTEGER, caseNumber INTEGER, plaintiff TEXT, defendant TEXT, witness TEXT, charge TEXT, status TEXT, hearingDate TEXT, verdict TEXT, sentence TEXT, sentenceStatus TEXT, rowID INTEGER AUTO_INCREMENT PRIMARY KEY);");
	}
	
	public function submitToDatabase(){
		// Connect to mySQL server
		$dbConn = new mysqli("localhost",'root');
		
		// If there is no database with the proper school year, make one.
		if(!$dbConn->select_db("jcdb".$GLOBALS['DBPrefix'])){
			$this->makeNewDatabase($dbConn);
		}
		
		$caseStateInsertString = "INSERT INTO casestate(plaintiff,witness,status,prefix,caseNumber,charge,defendant) VALUES('".$this->getData('plaintiff','string')."', '".$this->getData('witness','string')."', 'pndg', ";
		
		if($this->getData("prefix") == -1 && $this->getData("caseNumber") == -1){
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
			
			$sqlReturn = $dbConn->query("SELECT prefix, caseNumber FROM casehistory ORDER BY caseNumber DESC LIMIT 1;");
			
			$row = $sqlReturn->fetch_row();
			
			$sqlReturn->free();
			
			$this->addData("prefix",$row[0]);
			$this->addData("caseNumber",$row[1]);
			
			$newCaseInsertString = $caseStateInsertString.$this->getData('prefix').", ".$this->getData('caseNumber').", "; 
			
			$charges = $this->getData("charge");
			$defendants = $this->getData("defendant");
			
			foreach($charges as $charge){
				foreach($defendants as $defendant){
					$dbConn->query($newCaseInsertString."'".$charge."', '".$defendant."');");
				}
			}
			
			$dbConn->close();
			return "Case number: ".$this->getData("prefix")."-".$this->getData("caseNumber")." added to database.";
		}
		else{
			$updateCaseInsertString = $caseStateInsertString.$this->getData('prefix').", ".$this->getData('caseNumber').", ";
			// Update the complaint form record
			$queryString = "UPDATE casehistory SET ";
			
			$queryString = $queryString."formScan = '".$this->getData("formScan")."', ";
			
			foreach(self::$multiFields as $field){
				if(isset($this->data[$field])){
					$queryString = $queryString.$field." = '".$this->getData($field,'string')."', ";
				}
			}
			 
			foreach(self::$otherFields as $field){
				if(isset($this->data[$field])){
					$queryString = $queryString.$field." = '".$this->getData($field,'string')."', ";
				}
			}
			 
			if(strrpos($queryString, ", "))
				$queryString = substr($queryString,0,-2);
			
			$queryString = $queryString." WHERE caseNumber = ".$this->getData("caseNumber").";";
			
			$dbConn->query($queryString);
			
			// Update the charges in the database
			
			$defendants = $this->getData("defendant");
			$charges = $this->getData("charge");
			
			$sqlReturn = $dbConn->query("SELECT charge, defendant FROM casestate WHERE caseNumber = ".$this->getData("caseNumber").";");
			
			foreach($sqlReturn->fetch_all() as $row){
				$match = false;
				foreach($charges as $charge){
					foreach($defendants as $defendant){
						if($row[0] == $charge && $row[1] == $defendant){
							$match = true;
							break;
						}
					}
					if($match == true)
						break;
				}
				if($match == false){
					$dbConn->query("DELETE FROM casestate WHERE charge = '".$row[0]."' AND defendant = '".$row[1]."' AND caseNumber = ".$this->getData("caseNumber").";");
				}
			}
			$sqlReturn->free();
			
			$sqlReturn = $dbConn->query("SELECT charge, defendant FROM casestate WHERE caseNumber = ".$this->getData("caseNumber").";");
			$dbRows = $sqlReturn->fetch_all();
			
			foreach($charges as $charge){
					foreach($defendants as $defendant){
						$match = false;
						foreach($dbRows as $row){
							if($row[0] == $charge && $row[1] == $defendant){
								$match = true;
								break;
							}
						}
						if($match == false){
							$dbConn->query($updateCaseInsertString."'".$charge."', '".$defendant."');");
						}
					}
				}
			fclose($logFile);
			$dbConn->close();
			
			return "Case number: ".$this->getData("prefix")."-".$this->getData("caseNumber")." updated.";
			
		}
	}

	function __construct(){		
		foreach(self::$multiFields as $field){
		  $num = 1;
		  while(isset($_POST[$field.'-'.$num])){
				$this->addData($field,$_POST[$field.'-'.$num]);
				$num++;
		  }
		}
		
		foreach(self::$otherFields as $field){
			if(isset($_POST[$field])){
				$this->addData($field,$_POST[$field]);
			}
		}
		
		if(isset($_POST["prefix"]))
			$this->addData("prefix",$_POST["prefix"]);
		if(isset($_POST["caseNumber"]))
			$this->addData("caseNumber",$_POST["caseNumber"]);
		
		
		if(is_uploaded_file($_FILES['formScanFile']["tmp_name"])){
			if(isset($_POST['formScan'])){
				unlink($_POST['formScan']);
			}
			
			$scanDirPath = "./formScans".$GLOBALS['DBPrefix'];
			if(!file_exists($scanDirPath))
				mkdir($scanDirPath);
			
			$scanFileName = $scanDirPath."/".Date('U').".jpg";
			if(!move_uploaded_file($_FILES['formScanFile']['tmp_name'],$scanFileName)){
				echo "FAIL!";
				echo $_FILES['formScanFile']['tmp_name'];
				
			}
			else{
				$this->addData("formScan", $scanFileName);	
			}
		}
		else if(isset($_POST['formScan'])){
			$this->addData("formScan",$_POST['formScan']);
		}
	}
}
?>