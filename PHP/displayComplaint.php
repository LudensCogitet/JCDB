<?php
	require "./config.php";

	if($_REQUEST['caseNum'] && $_REQUEST['prefix'])
	  $dbConn = new mysqli($GLOBALS['config']['SQL_HOST'],$GLOBALS['config']['SQL_VIEW_USER']);
	  $dbConn->select_db($GLOBALS['config']['SQL_DB']);
	  
	  $result = $dbConn->query("SELECT * FROM casehistory WHERE caseNumber=".$_REQUEST['caseNum']." LIMIT 1;");
	  $row = $result->fetch_row();
	  $columns = $result->fetch_fields();
	  
	  $returnArray = [];
	  
	  for($i = 0; $i < count($row); $i++){
		   $returnArray[$columns[$i]->name] = $row[$i];
	  }
	  $dbConn->close();
	  
	  echo json_encode($returnArray);
?>