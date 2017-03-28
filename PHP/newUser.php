<div style="width:200px; height: 200px; position: absolute; top:50%; margin-top: -100px;left:50%; margin-left: -100px;">
<?php
	require './config.php';
	
	if(isset($_POST['username']) && isset($_POST['password'])){
		$dbConn = new mysqli($GLOBALS['config']['SQL_HOST'],$GLOBALS['config']['SQL_MODIFY_USER'],$GLOBALS['config']['SQL_MODIFY_PASS']);
		$dbConn->select_db($GLOBALS['config']['SQL_DB']);
		
		$pHash = password_hash($_POST['password'],PASSWORD_DEFAULT);
		
		echo $dbConn->query("INSERT INTO users(username,password,superuser) VALUES ('".$_POST['username']."','".$pHash."',"."1".");");
		echo "Done.";
		$dbConn->close();
	}
?>
<form method="POST">
Username: <input type="text" name="username" required></input>
Password: <input type="password" name="password" required></input>
<input type="submit"></input>
</form>
</div>
