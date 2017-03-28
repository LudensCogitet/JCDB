<?php
	require './config.php';
	session_start();
	
	if(isset($_SESSION['username'])){
		if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$changes = json_decode($_POST['changes']);
			
			$dbConn = new mysqli($GLOBALS['config']['SQL_HOST'],$GLOBALS['config']['SQL_MODIFY_USER'],$GLOBALS['config']['SQL_MODIFY_PASS']);
			$dbConn->select_db($GLOBALS['config']['SQL_DB']);
			
			$queryString = "UPDATE casestate SET ";
			
			$i = 0;
			$length = count((array)$changes);
			foreach($changes as $key=>$value){
				if($value == "")
					$queryString = $queryString.$key.'= NULL';
				else
					$queryString = $queryString.$key.'="'.$value.'"';
				if($i < $length -1){
					$queryString = $queryString.', ';
				
				$i++;
				}
			}
			
			$queryString = $queryString.' WHERE rowID='.$_POST['rowID'].';';
			
			$dbConn->query($queryString);
		
			$dbConn->close();
		}
	}
?>