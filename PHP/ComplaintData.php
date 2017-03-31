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
	
	private $deleteCase = false;
	
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
			$dbConn = new PDO("mysql:host=".$GLOBALS['config']['SQL_HOST'].
													";dbname=".$GLOBALS['config']['SQL_DB'],
													$GLOBALS['config']['SQL_MODIFY_USER'],
													$GLOBALS['config']['SQL_MODIFY_PASS'],
													[PDO::ATTR_PERSISTENT => true]);

			$caseStateInsertString = "INSERT INTO casestate(plaintiff,witness,status,prefix,caseNumber,charge,defendant) VALUES(?,?,'pndg',?,?,?,?)";
			$caseStateInsertParams = [$this->getData('plaintiff','string'),$this->getData('witness','string')];
			
			// If there is no prefix or case number, then generate a new complaint form record
			if($this->getData("prefix") == -1 && $this->getData("caseNumber") == -1){
				
				$statement = $dbConn->prepare("INSERT INTO casehistory(formScan,prefix,plaintiff,defendant,witness,dateOfIncident,timeOfIncident,location,charge,whatHappened) VALUES (?,?,?,?,?,?,?,?,?,?)");

				$statement->bindParam(1,$this->getData('formScan'));
				$statement->bindParam(2,$GLOBALS['currentYearCode']);
				$statement->bindParam(3,$this->getData('plaintiff','string'));
				$statement->bindParam(4,$this->getData('defendant','string'));
				$statement->bindParam(5,$this->getData('witness','string'));
				$statement->bindParam(6,$this->getData('dateOfIncident','string'));
				$statement->bindParam(7,$this->getData('timeOfIncident','string'));
				$statement->bindParam(8,$this->getData('location','string'));
				$statement->bindParam(9,$this->getData('charge','string'));
				$statement->bindParam(10,$this->getData('whatHappened'));
				
				$statement->execute();
				
				$statement = $dbConn->query("SELECT prefix, caseNumber FROM casehistory ORDER BY caseNumber DESC LIMIT 1");
				
				$row = $statement->fetch(PDO::FETCH_NUM);
				
				$this->addData("prefix",$row[0]);
				$this->addData("caseNumber",$row[1]);
				
				// Add charges to the casestate database
				$caseStateInsertParams[] = $this->getData('prefix');
				$caseStateInsertParams[] = $this->getData('caseNumber'); 
				
				$charges = $this->getData("charge");
				$defendants = $this->getData("defendant");
				
				
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
					$statement = $dbConn->prepare("DELETE FROM casehistory WHERE caseNumber = ?");
					$statement->execute([$this->getData("caseNumber")]);
					$statement = $dbConn->prepare("DELETE FROM casestate WHERE caseNumber = ?");
					$statement->execute([$this->getData("caseNumber")]);
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
								$queryParams[] = $this->getData($field,'string');
						}
					}
					
					if(strrpos($queryString, ", "))
						$queryString = substr($queryString,0,-2);
					
					$queryString = $queryString." WHERE caseNumber = ?";
					$queryParams[] = $this->getData("caseNumber");
					
					$statement = $dbConn->prepare($queryString);
					$statement->execute($queryParams);
					
					$statement = null;
					$dbConn = null;
				}
				else{
					$caseStateInsertParams[] = $this->getData('prefix');
					$caseStateInsertParams[] = $this->getData('caseNumber');
					
					$queryString = $queryString."formScan = ?, ";
					$queryParams[] = $this->getData("formScan");
					
					foreach(self::$multiFields as $field){
						if(isset($this->data[$field])){
							$queryString = $queryString.$field." = ?, ";
							$queryParams[] = $this->getData($field,'string');
						}
					}
					 
					foreach(self::$otherFields as $field){
						if(isset($this->data[$field])){
							$queryString = $queryString.$field." = ?, ";
							$queryParams[] = $this->getData($field,'string');
						}
					}
					 
					if(strrpos($queryString, ", "))
						$queryString = substr($queryString,0,-2);
					
					$queryString = $queryString." WHERE caseNumber = ?";
					$queryParams[] = $this->getData("caseNumber");
					
					$statement = $dbConn->prepare($queryString);
					$statement->execute($queryParams);
					
					// Update the charges in the casestate database
					
					$defendants = $this->getData("defendant");
					$charges = $this->getData("charge");
					
					$statement = $dbConn->prepare("SELECT charge, defendant FROM casestate WHERE caseNumber = ?");
					$statement->execute([$this->getData("caseNumber")]);
					
					$rows = $statement->fetchAll(PDO::FETCH_NUM);
					
					$dbConn->beginTransaction();
					$statement = $dbConn->prepare("DELETE FROM casestate WHERE charge = ? AND defendant = ? AND caseNumber = ?");
					
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
							$statement->execute($row);
						}
					}
					$dbConn->commit();
					
					$statement = $dbConn->prepare("SELECT charge, defendant FROM casestate WHERE caseNumber = ?");
					$statement->execute([$this->getData("caseNumber")]);
					
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
		if(isset($_POST["caseNumber"])){
			$this->addData("caseNumber",$_POST["caseNumber"]);
			if(isset($_POST["deleteComplaint"])){
				$this->deleteCase = true;
			}
		}
		
		
		if(is_uploaded_file($_FILES['formScanFile']["tmp_name"])){
			if(isset($_POST['formScan'])){
				unlink($_POST['formScan']);
			}
			
			$scanDirPath = "../formScans".$GLOBALS['currentYearCode'];
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