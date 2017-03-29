<div style="width:200px; height: 300px; position: absolute; top:50%; margin-top: -150px;left:50%; margin-left: -100px;">
<?php
	require './config.php';
	session_start();
	
	if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordConfirm'])){
		if(isset($_SESSION['superuser'])){
			if($_POST['password'] != $_POST['passwordConfirm']){
				echo "<h5>Passwords do not match.</h5>";
				return;
			}
			
			$dbConn = new mysqli($GLOBALS['config']['SQL_HOST'],$GLOBALS['config']['SQL_MODIFY_USER'],$GLOBALS['config']['SQL_MODIFY_PASS']);
			$dbConn->select_db($GLOBALS['config']['SQL_DB']);
			
			$pHash = password_hash($_POST['password'],PASSWORD_DEFAULT);
			
			$isSuperuser = 0;
			if(isset($_POST['isSuperuser']))
				$isSuperuser = 1;
		
			if(!$dbConn->query("INSERT INTO users(username,password,superuser) VALUES ('".$_POST['username']."','".$pHash."',".$isSuperuser.");")){
				if($dbConn->errno == 1062)
					echo "<h5>That account already exists.</h5>";
				else
					echo "<h5>unable to perform action (".$dbConn->error."). Please contact system admin.</h5>";
			}
			else
				echo "<h5>Account ".$_POST["username"]." added.</h5>";
			
			$dbConn->close();
		}
	}
?>
<form method="POST">
<span style="display: block;">Admin?<input type="checkbox" name="isSuperuser"></input></span>
Username: <input type="text" name="username" required></input>
Password: <input type="password" name="password" required></input>
Confirm password: <input type="password" name="passwordConfirm" required></input>
<input type="submit"></input>
</form>
<a href="../index.php">Return to database</a>
</div>
