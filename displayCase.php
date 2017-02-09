<?php
$prefix = DATE('Y');

	if($case = $_REQUEST['caseNum'])
	  $dbConn = new mysqli("localHost","root");
	  $dbConn->select_db("jcdb".$prefix);
	  
	  $result = $dbConn->query("SELECT * FROM casehistory WHERE caseNumber=".$case." LIMIT 1;");
	  $row = $result->fetch_row();
	  $columns = $result->fetch_fields();
	  
	  $returnArray = [];
	  
	  for($i = 0; $i < count($row); $i++){
		  if($columns[$i]->name == "formScan"){
			$returnArray[$columns[$i]->name] = "<img src='".$row[$i]."'>";  
		  }
		  else{
		    $returnArray[$columns[$i]->name] = $row[$i];
		  }
	  }
	  //$scanFile = $row[0];
	  
	  echo json_encode($returnArray);
?>
