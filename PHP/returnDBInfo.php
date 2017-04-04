<?php
	require './getYearCode.php';
	require './config.php';
  
	$searchCriteria = json_decode($_GET["criteria"]);
	$limits = json_decode($_GET["limits"]);
	
	if(isset($searchCriteria->prefix)){
		$prefix = $searchCriteria->prefix;
	}
	else{
		$prefix = '%';
	}
	
	try{
		$dbConn = new PDO("mysql:host=".$GLOBALS['config']['SQL_HOST'].
											";dbname=".$GLOBALS['config']['SQL_DB'],
											$GLOBALS['config']['SQL_VIEW_USER'],
											"",
											[PDO::ATTR_PERSISTENT => true]);
  
		if($searchCriteria == "all"){
			$sqlResult = $dbConn->query("SELECT SQL_CALC_FOUND_ROWS * FROM casestate ORDER BY caseNumber DESC LIMIT ".$limits->offset.",".$limits->count);
			$foundRows = $dbConn->query("SELECT FOUND_ROWS()");
		}
		else{
			$params = [];
			
			$queryString = "SELECT SQL_CALC_FOUND_ROWS * FROM casestate WHERE prefix LIKE ?";
			$params[] = $prefix;
			
			foreach($searchCriteria as $key => $val){
				$queryString = $queryString." AND ";
				
				if($key == "hearingDate" && preg_match_all('/[0-9]{4}-[0-9]{2}-[0-9]{2}/',$val,$matches) == 2){
					$queryString = $queryString."hearingDate >= ? AND hearingDate <= ?";
					$params[] = $matches[0][0];
					$params[] = $matches[0][1];
				}
				else{
					if($val == ""){
						$queryString = $queryString.$key." IS NULL";
					}
					else{
						$queryString = $queryString.$key." LIKE ?";
						$params[] = "%$val%";
					}
				}
			}
			
			
			$queryString = $queryString." LIMIT ".$limits->offset.",".$limits->count;
			
			$statement = $dbConn->prepare($queryString);
			
			if(!$statement->execute($params)){
				print "Error!:".$dbConn->errorInfo()[2]."<br/>";
				die();
			}
			$foundRows = $dbConn->query("SELECT FOUND_ROWS()");
			$sqlResult = $statement;
			//$statement->debugDumpParams();
		}
	}
	catch(Exception $e){
		print "Error!: ".$e->getMessage()."<br/>";
		return;
	}
	
	$rows = $sqlResult->fetchAll(PDO::FETCH_NUM);
	$numRows = (int)$foundRows->fetch()[0];
	
	if($limits->offset + count($rows) < $numRows)
		$moreRows = true;
	else
		$moreRows = false;
	
	echo json_encode([$moreRows,$rows]);
	
	$sqlResult = NULL;
	$statement = NULL;
	$dbConn = NULL;
?>