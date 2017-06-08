<head>
<link rel="stylesheet" type="text/css" href="CSS/UI.css">
<link rel="stylesheet" type="text/css" href="CSS/usersDisplay.css">
</head>
<div class="centerBox" style="width:300px;">
<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	session_start();
	try{
	$dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
											";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
											$GLOBALS['_JCDB_config']['SQL_MODIFY_USER'],
											$GLOBALS['_JCDB_config']['SQL_MODIFY_PASS'],
											[PDO::ATTR_PERSISTENT => true]);

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		echo "<div class='noteBox'>";
		if(isset($_POST['deleteUser'])){
			$statement = $dbConn->query("SELECT rowID FROM users WHERE superuser = 1");
			$numSupers = $statement->rowCount();
			$statement = $dbConn->prepare("SELECT username, superuser FROM users WHERE rowID = ?");
			$statement->execute([$_POST['rowID']]);
			$targetRow = $statement->fetch(PDO::FETCH_ASSOC);

			if($numSupers == 1 && $targetRow['superuser'] == 1){
				echo "Cannot delete last admin user";
			}
			else if($targetRow['username'] == $_SESSION['username']){
				echo "Cannot delete yourself!";
			}
			else{
				echo "User \"".$targetRow['username']."\" deleted";
				$statement = $dbConn->prepare("DELETE FROM users WHERE rowID = ?");
				$statement->execute([$_POST['rowID']]);
			}
		}
		else if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordConfirm'])){
			$_POST['username'] = htmlspecialchars($_POST['username']);
			$_POST['password'] = htmlspecialchars($_POST['password']);
			$_POST['passwordConfirm'] = htmlspecialchars($_POST['passwordConfirm']);

			if(isset($_SESSION['superuser'])){
				if($_POST['password'] != $_POST['passwordConfirm']){
					echo "Passwords do not match.";
				}
				else{
						$pHash = password_hash($_POST['password'],PASSWORD_DEFAULT);

						$isSuperuser = 0;
						if(isset($_POST['isSuperuser']))
							$isSuperuser = 1;

						$statement = $dbConn->prepare("INSERT INTO users(username,password,superuser) VALUES (?,?,?)");

						if(!$statement->execute([$_POST['username'],$pHash,$isSuperuser])){
							if($statement->errorCode() == 23000){
								echo "Account already exists";
							}
							else{
								echo "Unable to perform action (".$statement->errorCode()."). Please contact system admin.";
							}
						}
						else
							echo "Account \"".$_POST["username"]."\" added.";

						$statement = null;
				}
			}
		}
	echo "</div>";
	}
?>
<form name="newUserButton" method="POST">
<div style="float: right;">Admin<input style="vertical-align: -2px;" type="checkbox" name="isSuperuser"></input></div>
<div style="clear:both">Username</div>
<div><input type="text" name="username" required></input></div>
<div>Password</div>
<div><input type="password" name="password" required></input></div>
<div>Confirm Password</div>
<div><input type="password" name="passwordConfirm" required></input></div>
<input style="display:none;" type="submit"></input>
</form>
<div style='text-align: center;'>
<div class="UIButton buttonMedium sideBySide" onclick="document.newUserButton.submit();">Add User</div><br>
<div class="UIButton buttonMedium sideBySide" onclick="location.href='index.php';">Return To Database</div>
</div>
<div style="margin-top: 5px;" id="usersBox">
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

		echo "<td><form style='display:none' name='delete".$row['rowID']."' method='POST'>".
					"<input type='hidden' name='rowID' value='".$row['rowID']."'></input>".
					"<input type='hidden' name='deleteUser'></input></form>".
					"<div class='UIButton buttonTiny' onclick='document.delete".$row['rowID'].".submit();'>Delete</div>".
					"</td>";
		echo "</tr>";
	}
	$dbConn = null;
	$statement = null;
	}
	catch(Exception $e){
					echo "Error!:".$e->getMessage();
	}
?>
</table>
</div>
</div>
