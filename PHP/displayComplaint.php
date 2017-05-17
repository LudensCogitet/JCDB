<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

	if($_REQUEST['caseNum'] && $_REQUEST['prefix']){
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
												";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_USER'],
												$GLOBALS['_JCDB_config']['SQL_VIEW_PASS'],
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
