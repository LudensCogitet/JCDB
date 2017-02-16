<?php
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		$data = json_decode($_POST['changes']);
		$changes = $data[2];
		
		$dbConn = new mysqli("localHost","root");
		$dbConn->select_db("jcdb".$data[0]);
		
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
		
		$queryString = $queryString.' WHERE rowID='.$data[1].';';
		
		$dbConn->query($queryString);
	
		$dbConn->close();		
	}
?>