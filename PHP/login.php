<head>
<link rel="stylesheet" type="text/css" href="../CSS/UI.css">
</head>
<div style="text-align: center; width:200px; height: 200px; position: absolute; top:50%; margin-top: -100px;left:50%; margin-left: -100px;">
<?php
	require './config.php';
	
		if(isset($_SESSION['username']))
			unset($_SESSION['username']);
		if(isset($_SESSION['superuser']))
			unset($_SESSION['superuser']);
	
	if(isset($_POST['username']) && isset($_POST['password'])){
		
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
			
			if($statement->rowCount() > 0){
				$row = $statement->fetch(PDO::FETCH_ASSOC);
				if(password_verify($_POST['password'],$row['password']) || $_POST['password'] == $GLOBALS['config']['TEMP_SETUP_PASS']){
					session_start();
						$_SESSION['username'] = $row['username'];
					if($row['superuser'] == 1)
						$_SESSION['superuser'] = true;
				
				echo "<div style='margin-left: 14px' class='noteBox'>Logged in as<br>".$_SESSION['username']; 
				if(isset($_SESSION['superuser'])){ 
					echo "<br>with extended privileges.";
				}
				echo "</div>";
				}
				else{
					echo "<div style='margin-left: 14px' class='noteBox'>Wrong username or password</div>";
				}
			}
			else{
				echo "<div style='margin-left: 14px' class='noteBox'>Wrong username or password</div>";
			}
			$statement = null;
			$dbConn = null;
		}
		catch(Exception $e){
			print "Error!:".$e->getMessage()."<br>";
		}
	}
?>
<form style='margin-left:14px;' name='loginButton' method="POST">
Username <input type="text" name="username" required></input>
Password <input type="password" name="password" required></input>
</form>
<div style="clear:both;" class="UIButton buttonMedium" onclick="document.loginButton.submit();">Log In</div>
<div style="clear:both;" class="UIButton buttonMedium" onclick="location.href='../index.php';">Back To Database</div>
</div>
