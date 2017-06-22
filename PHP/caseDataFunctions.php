<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

	if(isset($_REQUEST['caseNumber']) && isset($_REQUEST['prefix'])){
		if(isset($_REQUEST['complaint'])){
			echo json_encode(grabCase($_REQUEST['prefix'],$_REQUEST['caseNumber'])[0]);
		}
		else if(isset($_REQUEST['caseNotes'])){
			echo json_encode(grabCaseNotes($_REQUEST['prefix'],$_REQUEST['caseNumber']));
		}
	}

	function grabAllCaseData($prefix,$caseNumber){
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
												";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_USER'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_PASS'],
												[PDO::ATTR_PERSISTENT => true]);

			$statement = $dbConn->prepare("SELECT * FROM caseentries WHERE prefix = ? AND caseNumber = ? ORDER BY rowID ASC;");
			$statement->execute([$prefix,$caseNumber]);
			$caseData = $statement->fetchALL(PDO::FETCH_ASSOC);
		}
		catch(Exception $e){
			print "Error!: ".$e->getMessage()."<br/>";
			return;
		}

		$dbConn = false;
		$statement = false;
	  return $caseData;
	}

	function grabCase($prefix,$caseNumber){
		return grabAllCaseData($prefix,$caseNumber)[0];
	}

	function grabContempts($prefix,$caseNumber){
		$contempts = array_slice(grabAllCaseData($prefix,$caseNumber),1);

		return count($contempts) > 0 ? $contempts : false;
	}

	function grabCaseNotes($prefix,$caseNumber){
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
												";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_USER'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_PASS'],
												[PDO::ATTR_PERSISTENT => true]);

			$statement = $dbConn->prepare("SELECT * FROM casenotes WHERE prefix = ? AND caseNumber = ? ORDER BY timeEntered ASC, rowID ASC;");
			$statement->execute([$prefix,$caseNumber]);
			$caseNotes = $statement->fetchALL(PDO::FETCH_ASSOC);
		}
		catch(Exception $e){
			print "Error!: ".$e->getMessage()."<br/>";
			return;
		}

		$dbConn = false;
		$statement = false;
		return $caseNotes;
	}

	function grabContemptStatus($prefix,$caseNumber){
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
												";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_USER'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_PASS'],
												[PDO::ATTR_PERSISTENT => true]);

			$statement = $dbConn->prepare("SELECT status, rowID FROM casestatus WHERE prefix = ? AND caseNumber = ? AND charge = 'contempt' OR charge = 'exile' ORDER BY rowID ASC;");
			$statement->execute([$prefix,$caseNumber]);
			$contempts = $statement->fetchALL(PDO::FETCH_ASSOC);
		}
		catch(Exception $e){
			print "Error!: ".$e->getMessage()."<br/>";
			return;
		}

		$dbConn = false;
		$statement = false;
		return count($contempts) > 0 ? $contempts : false;
	}
?>
