<div style="width:200px; height: 200px; position: absolute; top:50%; margin-top: -100px;left:50%; margin-left: -100px;">
<?php
	require './config.php';
	
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
				if(password_verify($_POST['password'],$row['password'])){
					session_start();
						$_SESSION['username'] = $row['username'];
					if($row['superuser'] == 1)
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
			$statement = null;
			$dbConn = null;
		}
		catch(Exception $e){
			print "Error!:".$e->getMessage()."<br>";
		}
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