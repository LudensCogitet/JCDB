<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

	if(isset($_REQUEST['caseNum']) && isset($_REQUEST['prefix'])){
		if(isset($_REQUEST['complaint'])){
			echo json_encode(grabCase($_REQUEST['prefix'],$_REQUEST['caseNum']));
		}
		else if(isset($_REQUEST['caseNotes'])){
			echo json_encode(grabCaseNotes($_REQUEST['prefix'],$_REQUEST['caseNum']));
		}
	}

	function grabCase($prefix,$caseNum){
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
												";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_USER'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_PASS'],
												[PDO::ATTR_PERSISTENT => true]);

			$statement = $dbConn->prepare("SELECT * FROM caseentries WHERE prefix = ? AND caseNumber = ? ORDER BY rowID ASC;");
			$statement->execute([$prefix,$caseNum]);
			$caseData = $statement->fetchALL(PDO::FETCH_ASSOC);
		}
		catch(Exception $e){
			print "Error!: ".$e->getMessage()."<br/>";
			return;
		}

		$dbConn = false;
		$statement = false;
	  return $caseData[0];
	}

	function grabCaseNotes($prefix,$caseNum){
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
												";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_USER'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_PASS'],
												[PDO::ATTR_PERSISTENT => true]);

			$statement = $dbConn->prepare("SELECT * FROM casenotes WHERE prefix = ? AND caseNumber = ? ORDER BY timeEntered ASC;");
			$statement->execute([$prefix,$caseNum]);
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
?>
