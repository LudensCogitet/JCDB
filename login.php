<head>
<link rel="stylesheet" type="text/css" href="../CSS/UI.css">
<script>
document.onreadystatechange = function(){
	if(document.readyState == "complete")
		document.getElementById("highlightOnLoad").focus();
}
</script>
</head>
<div class="centerBox">
<?php
	require 'PHP/config.php';
	if(isset($_POST['username']) && isset($_POST['password'])){
		session_start();
		if(isset($_SESSION['username'])){
			setcookie(session_name(),session_id(),1);
			session_unset();
			session_destroy();
			
			unset($_SESSION['username']);
			if(isset($_SESSION['superuser'])){
				unset($_SESSION['superuser']);
			}
			session_start();
		}
		
		$_POST['username'] = htmlspecialchars($_POST['username']);
		$_POST['password'] = htmlspecialchars($_POST['password']);
		try{
			$dbConn = new PDO("mysql:host=".$GLOBALS['config']['SQL_HOST'].
												";dbname=".$GLOBALS['config']['SQL_DB'],
												$GLOBALS['config']['SQL_MODIFY_USER'],
												$GLOBALS['config']['SQL_MODIFY_PASS'],
												[PDO::ATTR_PERSISTENT => true]);
			
			$statement = $dbConn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1;");
			$statement->execute([$_POST['username']]);
			echo "<div class='noteBox'>";
			if($statement->rowCount() > 0){
				$row = $statement->fetch(PDO::FETCH_ASSOC);
				if(password_verify($_POST['password'],$row['password']) || $_POST['password'] == $GLOBALS['config']['TEMP_SETUP_PASS']){
					$_SESSION['username'] = $row['username'];
					if($row['superuser'] == 1)
						$_SESSION['superuser'] = true;
				
					echo "Logged in as<br>\"".$_SESSION['username']."\""; 
					if(isset($_SESSION['superuser'])){ 
						echo "<br>with extended privileges.";
					}
				}
				else{
					echo "Wrong username or password";
				}
			}
			else{
				echo "Wrong username or password";
			}
			$statement = null;
			$dbConn = null;
		}
		catch(Exception $e){
			print "Error!:".$e->getMessage()."<br>";
		}
		echo "</div>";
	}
?>
<form name='loginButton' method="POST">
<div>Username</div>
<div><input id="highlightOnLoad" type="text" name="username" required></input></div>
<div>Password</div>
<div><input type="password" name="password" required></input></div>
<input style='display:none;' type="submit"></input>
</form>
<div class="UIButton buttonMedium" onclick="document.loginButton.submit();">Log In</div><br>
<div class="UIButton buttonMedium" onclick="location.href='../index.php';">Back To Database</div>
</div>
