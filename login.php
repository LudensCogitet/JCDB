<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	require_once 'PHP/databaseConnect.php';
	require_once 'PHP/login/checkRequests.php';
	session_start();
?>

<head>
	<link rel="stylesheet" type="text/css" href="CSS/UI.css">
	<script>
		document.onreadystatechange = function(){
			if(document.readyState == "complete")
				document.getElementById("highlightOnLoad").focus();
		}
	</script>
</head>
<div class="centerBox">
	<?php
		$dbConn = databaseConnect('read');
		checkRequests($dbConn);
	?>
	<form name='loginButton' method="POST">
		<div>Username</div>
		<div><input id="highlightOnLoad" type="text" name="username" required></input></div>
		<div>Password</div>
		<div><input type="password" name="password" required></input></div>
		<button type="submit" class="UIButton buttonMedium stackable">Log In</button>
	</form>
	<div class="UIButton buttonMedium sideBySide" onclick="location.href='index.php';">Back To Database</div>
</div>
