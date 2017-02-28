<?php
	require 'getDBIdent.php';
	
	if(isset($_GET['dataBaseIdent']))
		$prefix = $_GET['dataBaseIdent'];
	else
		$prefix = getDBIdent();
  
	$searchCriteria = json_decode($_GET["criteria"]);
	
  $dbConn = new mysqli("localHost","root");
  $dbConn->select_db("jcdb".$prefix);
	
	if($searchCriteria == "all")
		$sqlResult = $dbConn->query("SELECT * FROM casestate ORDER BY caseNumber");
	else{
		$queryString = "SELECT * FROM casestate WHERE ";
		$i = 0;
		foreach($searchCriteria as $key => $val){
			if($i == 0)
				$queryString = $queryString.$key." LIKE '%".$val."%'";
			else
				$queryString = $queryString." AND ".$key." LIKE '%".$val."%'";
			
			$i++;
		}
		$queryString = $queryString.";";
		
		$sqlResult = $dbConn->query($queryString);
	}
	$dbConn->close();
	
  $values = [];
  
  while($row = $sqlResult->fetch_row()){
	  $values[] = $row;
  }
	
  echo json_encode($values);
?>