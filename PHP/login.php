<div style="width:200px; height: 200px; position: absolute; top:50%; margin-top: -100px;left:50%; margin-left: -100px;">
<?php
	require './config.php';
	
	if(isset($_POST['username']) && isset($_POST['password'])){
		$dbConn = new mysqli($GLOBALS['config']['SQL_HOST'],$GLOBALS['config']['SQL_MODIFY_USER'],$GLOBALS['config']['SQL_MODIFY_PASS']);
		$dbConn->select_db($GLOBALS['config']['SQL_DB']);
		$userInfo = $dbConn->query("SELECT * FROM users WHERE username='".$_POST['username']."' LIMIT 1;");
		if($userInfo->num_rows > 0){
			$row = $userInfo->fetch_row();
			if(password_verify($_POST['password'],$row[1])){
				session_start();
					$_SESSION['username'] = $row[0];
				if($row[2] == 1)
					$_SESSION['superuser'] = true;
			
			echo "Currently signed in as ".$_SESSION['username']; 
			if(isset($_SESSION['superuser'])) 
				echo " with extended privileges.";
			}
			else{
				echo "Wrong username or password";
			}
		}
		else{
			echo "Wrong username or password";
		}
		$userInfo->free();
		$dbConn->close();
	}
	else{
?>
<form method="POST">
Username: <input type="text" name="username" required></input>
Password: <input type="password" name="password" required></input>
<input type="submit"></input>
</form>
</div>
<?php
}
?>
<div><a href="../index.php">Back to database</a></div>
</div>