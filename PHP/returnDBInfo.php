<?php
	require './getYearCode.php';
  
	$searchCriteria = json_decode($_GET["criteria"]);
	
	if(isset($searchCriteria->prefix)){
		$prefix = $searchCriteria->prefix;
		if(!isset($searchCriteria->caseNumber))
			$searchCriteria->caseNumber = "";
	}
	else{
		$prefix = getYearCode();
	}
	
  $dbConn = new mysqli("localHost","root");
  $dbConn->select_db("jcdb");
	
	if($searchCriteria == "all")
		$sqlResult = $dbConn->query("SELECT * FROM casestate ORDER BY caseNumber");
	else{
		$queryString = "SELECT * FROM casestate WHERE prefix = ".$prefix." ";
		
		foreach($searchCriteria as $key => $val){
			$queryString = $queryString." AND ";
			
			if($key == "hearingDate" && preg_match_all('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$val,$matches) == 2){
				$queryString = $queryString."hearingDate >= '".$matches[0][0]."' AND hearingDate <= '".$matches[0][1]."' ";
			}
			else{
				if($val == "")
					$queryString = $queryString.$key." IS NULL";
				else
					$queryString = $queryString.$key." LIKE '%".$val."%'";
			}
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