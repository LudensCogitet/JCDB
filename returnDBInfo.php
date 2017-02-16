<?php
  $prefix = DATE('Y');
  
  $dbConn = new mysqli("localHost","root");
  $dbConn->select_db("jcdb".$prefix);
  
  $sqlResult = $dbConn->query("SELECT * FROM casestate ORDER BY caseNumber");
  
	$dbConn->close();
	
  $values = [];
  
  while($row = $sqlResult->fetch_row()){
	  $values[] = $row;
  }
  
  echo json_encode($values);
?>