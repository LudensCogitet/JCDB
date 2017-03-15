<?php
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$changes = json_decode($_POST['changes']);
		
		$dbConn = new mysqli("localHost","root");
		$dbConn->select_db("jcdb".$_POST['prefix']);
		
		$queryString = "UPDATE casestate SET ";
		
		$i = 0;
		$length = count((array)$changes);
		foreach($changes as $key=>$value){
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
?>