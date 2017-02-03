<?php
$prefix = DATE('Y');

	if($case = $_REQUEST['caseNum'])
	  $dbConn = new mysqli("localHost","root");
	  $dbConn->select_db("jcdb".$prefix);
	  
	  $result = $dbConn->query("SELECT formScan FROM casehistory WHERE caseNumber=".$case." LIMIT 1;");
	  $row = $result->fetch_row();
	  $scanFile = $row[0];
	  
	  echo "<img src='".$scanFile."'>";
?>
