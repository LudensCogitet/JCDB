<?php
	require './getYearCode.php';
	require './config.php';
  
	$searchCriteria = json_decode($_GET["criteria"]);
	
	if(isset($searchCriteria->prefix)){
		$prefix = $searchCriteria->prefix;
		if(!isset($searchCriteria->caseNumber))
			$searchCriteria->caseNumber = "";
	}
	else{
		$prefix = getYearCode();
	}
	
	try{
		$dbConn = new PDO("mysql:host=".$GLOBALS['config']['SQL_HOST'].
											";dbname=".$GLOBALS['config']['SQL_DB'],
											$GLOBALS['config']['SQL_VIEW_USER'],
											"",
											[PDO::ATTR_PERSISTENT => true]);
  
		if($searchCriteria == "all"){
			$sqlResult = $dbConn->query("SELECT * FROM casestate ORDER BY caseNumber");
			if(!$sqlResult){	
				print "Error!:".$dbConn->errorInfo()[2]."<br/>";
				return;
			}
		}
		else{
			$params = [];
			
			$queryString = "SELECT * FROM casestate WHERE prefix = ?";
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
			
			$statement = $dbConn->prepare($queryString);
			
			if(!$statement->execute($params)){
				print "Error!:".$dbConn->errorInfo()[2]."<br/>";
				die();
			}
			$sqlResult = $statement;
			//$statement->debugDumpParams();
		}
	}
	catch(Exception $e){
		print "Error!: ".$e->getMessage()."<br/>";
		return;
	}
	
	echo json_encode($sqlResult->fetchAll(PDO::FETCH_NUM));
	
	$sqlResult = NULL;
	$statement = NULL;
	$dbConn = NULL;
?>