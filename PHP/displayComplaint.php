<?php
	if($_REQUEST['caseNum'] && $_REQUEST['prefix'])
	  $dbConn = new mysqli("localHost","root");
	  $dbConn->select_db("jcdb");
	  
	  $result = $dbConn->query("SELECT * FROM casehistory WHERE caseNumber=".$_REQUEST['caseNum']." LIMIT 1;");
	  $row = $result->fetch_row();
	  $columns = $result->fetch_fields();
	  
	  $returnArray = [];
	  
	  for($i = 0; $i < count($row); $i++){
		   $returnArray[$columns[$i]->name] = $row[$i];
	  }
	  //$scanFile = $row[0];
	  
	  echo json_encode($returnArray);
?>