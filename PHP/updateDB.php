<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	session_start();
	
	if(isset($_SESSION['username'])){
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$changes = json_decode($_POST['changes']);
		
			try{
				$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
													";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
													$GLOBALS['_JCDB_config']['SQL_MODIFY_USER'],
													$GLOBALS['_JCDB_config']['SQL_MODIFY_PASS'],
													[PDO::ATTR_PERSISTENT => true]);
			
				$queryString = "UPDATE casestate SET ";
				$queryParams = [];
			
				$i = 0;
				$length = count((array)$changes);
				foreach($changes as $key=>$value){
					if($value == "")
						$queryString = $queryString.$key.' = NULL';
					else{
						$queryString = $queryString.$key.' = ?';
						$queryParams[] = htmlspecialchars($value);
					}
				
					if($i < $length -1){
						$queryString = $queryString.', ';
				
						$i++;
					}
				}
			
				$queryString = $queryString.' WHERE rowID = ?';
				$queryParams[] = $_POST['rowID'];
				
				$statement = $dbConn->prepare($queryString);
				$statement->execute($queryParams);
			}
			catch(Exception $e){
				print "Error!:".$e->getMessage();
			}
		}
		$dbConn = null;
		$statement = null;
	}
?>