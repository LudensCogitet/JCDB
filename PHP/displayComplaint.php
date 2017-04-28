<?php
	require "./config.php";

	if($_REQUEST['caseNum'] && $_REQUEST['prefix']){
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['config']['SQL_HOST'].
												";dbname=".$GLOBALS['config']['SQL_DB'],
												$GLOBALS['config']['SQL_VIEW_USER'],
												$GLOBALS['config']['SQL_VIEW_PASS'],
												[PDO::ATTR_PERSISTENT => true]);
			
			$statement = $dbConn->prepare("SELECT * FROM casehistory WHERE prefix = ? AND caseNumber = ? LIMIT 1;");
			$statement->execute([$_REQUEST['prefix'],$_REQUEST['caseNum']]);
			$row = $statement->fetch(PDO::FETCH_ASSOC);
		}
		catch(Exception $e){
			print "Error!: ".$e->getMessage()."<br/>";
			return;
		}
		
		$dbConn = false;
		$statement = false;
	  echo json_encode($row);
	}
?>