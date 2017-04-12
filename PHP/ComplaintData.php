<?php
require './getYearCode.php';
require './config.php';

function sanitize($var){
	$var = trim($var);
	$var = htmlspecialchars($var);
	return $var;
}

$currentYearCode = getYearCode();

class ComplaintData{
	public static $multiFields = ["plaintiff","defendant","witness","charge","dateOfIncident","timeOfIncident","location","hearingDate"];
	public static $otherFields = ["whatHappened","hearingNotes"];
	public static $hearingFields = ["hearingDate","hearingNotes"];
	
	private $newComplaint = true;
	
	private $data = ["formScan"		  => "",
					 "prefix"				=> -1,
					 "caseNumber"	  => -1,
					 "plaintiff" 	  => "",
					 "defendant" 	  => "",
					 "witness"				=> "",
					 "charge"					=> "",
					 "dateOfIncident" => "",
					 "timeOfIncident" => "",
					 "location"  	  => "",
					 "whatHappened"   => "",
					 "hearingDate"			=> "",
					 "hearingNotes"		=>	""];
	
	private $deleteCase = false;
	
	public function addData($field, $entry){
		if(!array_key_exists($field, $this->data)){
			return false;
		}
		else{
			if(is_array($entry)){
				$this->data[$field] = sanitize(implode(', ',$entry));
				return true;
			}
			else{
				$this->data[$field] = sanitize($entry);
				return true;
			}
		}
	}
	
	public function checkDeleteScan(){
		if($this->newComplaint == true){
			unlink($this->data['formScan']);
		}
	}
	
	public function encodeData(){
		return json_encode($this->data);
	}
	
	public function getData($field, $as = "string/single"){
		if(!array_key_exists($field, $this->data)){
				return false;
		}
		else{
			if($as == "string/single")
				return $this->data[$field];
			else if($as == "array"){
				return explode(', ',$this->data[$field]);
			}
		}
	}
	
	private function renameScan(){
		$oldName = $this->getData('formScan');
		$newName = "../formScans".$GLOBALS['currentYearCode']."/SCAN_".$this->getData('prefix').$this->getData('caseNumber').".jpg";
		if(strstr($oldName,'TEMPIMG_') != false){
			if(!rename($oldName,$newName)){
				echo "Could not rename form scan '".$oldName."'";
			}
			else{
				$this->addData('formScan',$newName);
			}
		}
	}
	
	private function makeNewDatabase($dbConn){
		$dbConn->query("CREATE DATABASE jcdb;");
		$dbConn->select_db("jcdb");
		$dbConn->query("CREATE TABLE casehistory(prefix INTEGER, caseNumber INTEGER AUTO_INCREMENT PRIMARY KEY, formScan TEXT, plaintiff TEXT, defendant TEXT, witness TEXT, dateOfIncident TEXT, timeOfIncident TEXT, location TEXT, charge TEXT, whatHappened TEXT, hearingDate TEXT, hearingNotes TEXT);");
		$dbConn->query("CREATE TABLE casestate(prefix INTEGER, caseNumber INTEGER, plaintiff TEXT, defendant TEXT, witness TEXT, charge TEXT, status TEXT, hearingDate TEXT, verdict TEXT, sentence TEXT, sentenceStatus TEXT, rowID INTEGER AUTO_INCREMENT PRIMARY KEY);");
	}
	
	public function submitToDatabase(){
		try{
			if(!isset($_SESSION["username"])){
				echo "No user signed in";
				return;
			}
			
			// Connect to mySQL server
			$dbConn = NULL;
			try{
				$dbConn = new PDO("mysql:host=".$GLOBALS['config']['SQL_HOST'].
														";dbname=".$GLOBALS['config']['SQL_DB'],
														$GLOBALS['config']['SQL_MODIFY_USER'],
														$GLOBALS['config']['SQL_MODIFY_PASS'],
														[PDO::ATTR_PERSISTENT => true]);
			}
			catch(PDOException $e){
				echo "Exception: ".$e->errorInfo.$dbConn;
				return;
			}
			
			$caseStateInsertString = "INSERT INTO casestate(plaintiff,witness,status,prefix,caseNumber,charge,defendant) VALUES(?,?,'pndg',?,?,?,?)";
			$caseStateInsertParams = [$this->getData('plaintiff'),$this->getData('witness')];
			
			// If there is no prefix or case number, then generate a new complaint form record
			if($this->getData("prefix") == -1 && $this->getData("caseNumber") == -1){
				
				$statement = $dbConn->prepare("INSERT INTO casehistory(prefix,plaintiff,defendant,witness,dateOfIncident,timeOfIncident,location,charge,whatHappened) VALUES (?,?,?,?,?,?,?,?,?)");
				
				$params = [];
				$params[] = $GLOBALS['currentYearCode'];
				$params[] = $this->getData('plaintiff');
				$params[] = $this->getData('defendant');
				$params[] = $this->getData('witness');
				$params[] = $this->getData('dateOfIncident');
				$params[] = $this->getData('timeOfIncident');
				$params[] = $this->getData('location');
				$params[] = $this->getData('charge');
				$params[] = $this->getData('whatHappened');
				
				$statement->execute($params);
				
				$statement = $dbConn->query("SELECT caseNumber FROM casehistory WHERE prefix = ".$GLOBALS['currentYearCode']." ORDER BY caseNumber DESC LIMIT 1");
				
				$row = $statement->fetch(PDO::FETCH_NUM);
				
				$this->addData("prefix",$GLOBALS['currentYearCode']);
				$this->addData("caseNumber",$row[0]);
				$this->renameScan();
				$dbConn->query("UPDATE casehistory SET formScan = '".$this->getData('formScan')."' WHERE prefix = ".$this->getData('prefix')." AND caseNumber = ".$this->getData('caseNumber'));
				
				// Add charges to the casestate database
				$caseStateInsertParams[] = $this->getData('prefix');
				$caseStateInsertParams[] = $this->getData('caseNumber'); 
				
				$charges = $this->getData("charge","array");
				$defendants = $this->getData("defendant","array");
				
				
				$statement = $dbConn->prepare($caseStateInsertString);
				
				try{
					$dbConn->beginTransaction();
					
					foreach($charges as $charge){
						foreach($defendants as $defendant){
							$statement->execute(array_merge($caseStateInsertParams,[$charge,$defendant]));
							}
						}
					$dbConn->commit();
				}
				catch(Exception $e){
					$dbConn->rollBack();
					throw($e);
				}
				
				$statement = null;
				$dbConn = null;
				
				return "Case number ".$this->getData("prefix")."-".$this->getData("caseNumber")." added to database.";
			}
			else{		// Otherwise, update an existing complaint form record
			
				if($this->deleteCase == true && isset($_SESSION["superuser"])){	// Or just delete it, if that's what the user wants
					$statement = $dbConn->prepare("DELETE FROM casehistory WHERE caseNumber = ? AND prefix = ?");
					$statement->execute([$this->getData("caseNumber"),$this->getData("prefix")]);
					$statement = $dbConn->prepare("DELETE FROM casestate WHERE caseNumber = ? AND prefix = ?");
					$statement->execute([$this->getData("caseNumber"),$this->getData("prefix")]);
					unlink($this->getData("formScan"));
					$dbConn = null;
					$statement = null;
					return "Case number ".$this->getData("prefix")."-".$this->getData("caseNumber")." deleted.";
				}
				
				$queryString = "UPDATE casehistory SET ";
				$queryParams = [];
				
				if(!isset($_SESSION['superuser'])){
						foreach(self::$hearingFields as $field){
							if(isset($this->data[$field])){
								$queryString = $queryString.$field." = ?, ";
								$queryParams[] = $this->getData($field);
						}
					}
					
					if(strrpos($queryString, ", "))
						$queryString = substr($queryString,0,-2);
					
					$queryString = $queryString." WHERE caseNumber = ? AND prefix = ?";
					$queryParams[] = $this->getData("caseNumber");
					$queryParams[] = $this->getData("prefix");
					
					$statement = $dbConn->prepare($queryString);
					$statement->execute($queryParams);
					
					$statement = null;
					$dbConn = null;
				}
				else{
					$caseStateInsertParams[] = $this->getData('prefix');
					$caseStateInsertParams[] = $this->getData('caseNumber');
					
					$this->renameScan();
					$queryString = $queryString."formScan = ?, ";
					$queryParams[] = $this->getData("formScan");
					
					foreach(self::$multiFields as $field){
						if(isset($this->data[$field])){
							$queryString = $queryString.$field." = ?, ";
							$queryParams[] = $this->getData($field);
						}
					}
					 
					foreach(self::$otherFields as $field){
						if(isset($this->data[$field])){
							$queryString = $queryString.$field." = ?, ";
							$queryParams[] = $this->getData($field);
						}
					}
					 
					if(strrpos($queryString, ", "))
						$queryString = substr($queryString,0,-2);
					
					$queryString = $queryString." WHERE caseNumber = ? AND prefix = ?";
					$queryParams[] = $this->getData("caseNumber");
					$queryParams[] = $this->getData("prefix");
					
					$statement = $dbConn->prepare($queryString);
					$statement->execute($queryParams);
					
					// Update the charges in the casestate database
					
					$statement = $dbConn->prepare("UPDATE casestate SET plaintiff = ?, witness = ? WHERE prefix = ? AND caseNumber = ?");
					$statement->execute($caseStateInsertParams);
					
					$defendants = $this->getData("defendant","array");
					$charges = $this->getData("charge","array");
					
					$statement = $dbConn->prepare("SELECT charge, defendant FROM casestate WHERE caseNumber = ? AND prefix = ?");
					$statement->execute([$this->getData("caseNumber"),$this->getData("prefix")]);
					
					$rows = $statement->fetchAll(PDO::FETCH_NUM);
					
					$dbConn->beginTransaction();
					$statement = $dbConn->prepare("DELETE FROM casestate WHERE charge = ? AND defendant = ? AND caseNumber = ? AND prefix = ?");
					
					foreach($rows as $row){
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
							$row[] = $this->getData("caseNumber");
							$row[] = $this->getData("prefix");
							$statement->execute($row);
						}
					}
					$dbConn->commit();
					
					$statement = $dbConn->prepare("SELECT charge, defendant FROM casestate WHERE caseNumber = ? AND prefix = ?");
					$statement->execute([$this->getData("caseNumber"),$this->getData("prefix")]);
					
					$rows = $statement->fetchAll(PDO::FETCH_NUM);
					
					$dbConn->beginTransaction();
					$statement = $dbConn->prepare($caseStateInsertString);
					
					foreach($charges as $charge){
						foreach($defendants as $defendant){
							$match = false;
							foreach($rows as $row){
								if($row[0] == $charge && $row[1] == $defendant){
									$match = true;
									break;
								}
							}
							if($match == false){
								$statement->execute(array_merge($caseStateInsertParams, [$charge,$defendant]));
							}
						}
					}
					$dbConn->commit();
					
					$statement = null;
					$dbConn = null;
				}
				
				return "Case number ".$this->getData("prefix")."-".$this->getData("caseNumber")." updated.";
			}
		}
		catch(Exception $e){
			print "Failed to add or modifiy case record:".$e->getMessage();
			return;
		}
	}

	function __construct(){
		if(!isset($_SESSION["username"])){
			echo "No user signed in";
			return;
		}
		foreach(self::$multiFields as $field){
		  $num = 1;
			$array = [];
			while(isset($_POST[$field.'-'.$num])){
				$array[] = $_POST[$field.'-'.$num];
				$num++;
		  }
			$this->addData($field,$array);
		}
		
		foreach(self::$otherFields as $field){
			if(isset($_POST[$field])){
				$this->addData($field,$_POST[$field]);
			}
		}
		
		if(isset($_POST["prefix"])){
			$this->addData("prefix",$_POST["prefix"]);
			if(isset($_POST["caseNumber"])){
				$this->addData("caseNumber",$_POST["caseNumber"]);
				$this->newComplaint = false;
				if(isset($_POST["deleteComplaint"])){
					$this->deleteCase = true;
				}
			}
		}
		
		if(isset($_FILES['formScanFile'])){
			if(is_uploaded_file($_FILES['formScanFile']["tmp_name"])){
				if(imagecreatefromjpeg($_FILES['formScanFile']["tmp_name"]) == false){
					echo "Please upload form scan in .jpg format.";
					return;
				}
				
				if($this->newComplaint || isset($_SESSION['superuser'])){
					
					if(isset($_POST['formScan'])){
						unlink($_POST['formScan']);
					}

					$scanDirPath = "../formScans".$GLOBALS['currentYearCode'];
					if(!file_exists($scanDirPath))
						mkdir($scanDirPath);
						
					$scanFileName = $scanDirPath."/TEMPIMG_".$_SESSION['username'].".jpg";
					if(!move_uploaded_file($_FILES['formScanFile']['tmp_name'],$scanFileName)){
						echo "FAIL!";
						echo $_FILES['formScanFile']['tmp_name'];
					}
					else{
						$this->addData("formScan", $scanFileName);	
					}
				}
			}
			else if(isset($_POST['formScan'])){
				$this->addData("formScan",$_POST['formScan']);
			}
		}
		else if(isset($_POST['formScan'])){
			$this->addData("formScan",$_POST['formScan']);
		}
	}
}
?>