<?php
	require 'getDBIdent.php';
	
	if(isset($_GET['dataBaseIdent']))
		$prefix = $_GET['dataBaseIdent'];
	else
		$prefix = getDBIdent();
  
  $dbConn = new mysqli("localHost","root");
  $dbConn->select_db("jcdb".$prefix);
	
	if($_GET["column"] == "all" && $_GET["value"] == "all")
		$sqlResult = $dbConn->query("SELECT * FROM casestate ORDER BY caseNumber");
	else
		$sqlResult = $dbConn->query("SELECT * FROM casestate WHERE ".$_GET["column"]." = '".$_GET["value"]."' ORDER BY caseNumber");
  
	$dbConn->close();
	
  $values = [];
  
  while($row = $sqlResult->fetch_row()){
	  $values[] = $row;
  }
	
  echo json_encode($values);
?>