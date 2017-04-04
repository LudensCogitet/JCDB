<div style="width:200px; height: 300px; position: absolute; top:50%; margin-top: -150px;left:50%; margin-left: -100px;">
<?php
	require './config.php';
	session_start();
	
	try{
		$dbConn = new PDO("mysql:host=".$GLOBALS['config']['SQL_HOST'].
											";dbname=".$GLOBALS['config']['SQL_DB'],
											$GLOBALS['config']['SQL_MODIFY_USER'],
											$GLOBALS['config']['SQL_MODIFY_PASS'],
											[PDO::ATTR_PERSISTENT => true]);
		if(isset($_POST['deleteUser'])){
			$statement = $dbConn->query("SELECT rowID FROM users WHERE superuser = 1");
			$numSupers = $statement->rowCount();
			$statement = $dbConn->prepare("SELECT username, superuser FROM users WHERE rowID = ?");
			$statement->execute([$_POST['rowID']]);
			$targetRow = $statement->fetch(PDO::FETCH_ASSOC);
			
			if($numSupers == 1 && $targetRow['superuser'] == 1){
				echo "Cannot delete last admin user<br>";
			}
			else if($targetRow['username'] == $_SESSION['username']){
				echo "Cannot delete yourself!<br>";
			}
			else{
				$statement = $dbConn->prepare("DELETE FROM users WHERE rowID = ?");
				echo $_POST['rowID'];
				$statement->execute([$_POST['rowID']]);
			}
		}
		else if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordConfirm'])){
			$_POST['username'] = htmlspecialchars($_POST['username']);
			$_POST['password'] = htmlspecialchars($_POST['password']);
			$_POST['passwordConfirm'] = htmlspecialchars($_POST['passwordConfirm']);
			
			if(isset($_SESSION['superuser'])){
				if($_POST['password'] != $_POST['passwordConfirm']){
					print "<h5>Passwords do not match.</h5>";
				}
				else{					
						$pHash = password_hash($_POST['password'],PASSWORD_DEFAULT);
						
						$isSuperuser = 0;
						if(isset($_POST['isSuperuser']))
							$isSuperuser = 1;
						
						$statement = $dbConn->prepare("INSERT INTO users(username,password,superuser) VALUES (?,?,?)");
						
						if(!$statement->execute([$_POST['username'],$pHash,$isSuperuser])){
							if($statement->errorCode() == 23000){
								echo "<h5>Account already exists</h5>";
							}
							else{
								echo "<h5>unable to perform action (".$statement->errorCode()."). Please contact system admin.</h5>";
							}
						}
						else
							echo "<h5>Account ".$_POST["username"]." added.</h5>";
						
						$statement = null;
				}
			}
		}
	}
	catch(Exception $e){
					echo "Error!:".$e->getMessage();
	}
?>
<form method="POST">
Add user<br>
<span style="display: block;">Admin?<input type="checkbox" name="isSuperuser"></input></span>
Username: <input type="text" name="username" required></input>
Password: <input type="password" name="password" required></input>
Confirm password: <input type="password" name="passwordConfirm" required></input>
<input type="submit"></input>
</form>
<a href="../index.php">Return to database</a>
<br>Current Users<br>
<table style="text-align: center;">
<th>Username</th>
<th>Admin</th>
<?php
	$statement = $dbConn->query("SELECT username, superuser, rowID FROM users ORDER BY username");
	
	foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $row){
		echo "<tr><td>".$row['username']."</td>";
		if($row['superuser'] == 1)
			echo "<td>Yes</td>";
		else
			echo "<td>No</td>";
		
		echo "<td><form method='POST'>".
					"<input type='hidden' name='rowID' value='".$row['rowID']."'></input>".
					"<input type='submit' name='deleteUser' value='delete'></input>".
					"</form></td>";
		
		echo "</tr>";
	}
	$dbConn = null;
	$statement = null;
?>
</table>
</div>