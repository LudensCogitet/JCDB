<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	session_start();

	require_once 'PHP/checkUserPermissions.php';
	checkUserPermissions('superuser');

	require_once 'PHP/databaseConnect.php';
	require_once 'PHP/manageUsers/checkRequests.php';
	require_once 'PHP/manageUsers/getUserList.php';

	$dbConn = databaseConnect('write');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
 		<meta http-equiv="X-UA-Compatible" content="IE=edge">
 		<meta name="viewport" content="width=device-width, initial-scale=1">
 		<title>Manage users</title>
		<link rel="stylesheet" type="text/css" href="CSS/UI.css">
		<link rel="stylesheet" type="text/css" href="CSS/usersDisplay.css">
	</head>
	<body>
		<div class="centerBox" style="width:300px;">
			<form name="newUserButton" method="POST">
				<?php checkRequests($dbConn); ?>
				<div >Username</div>
				<div style='position: relative;'>
					<div style="position: absolute; right: 0; top: -1em; margin-right: -5px;">Admin<input style="vertical-align: -2px;" type="checkbox" name="isSuperuser"></input></div>
					<input type="text" name="username" required='required'></input>
				</div>
				<div>Password</div>
				<div><input type="password" name="password" required='required'></input></div>
				<div>Confirm Password</div>
				<div><input type="password" name="passwordConfirm" required='required'></input></div>
				<button type="submit" class="UIButton buttonMedium stackable">Add User</button>
			</form>
			<div style='text-align: center;'>
				<div class="UIButton buttonMedium sideBySide" onclick="location.href='index.php';">Return To Database</div>
			</div>
			<div style="margin-top: 5px;" id="usersBox">
			<?php getUserList($dbConn); ?>
			</div>
		</div>
	</body>
</html>
<?php $dbConn = null; ?>
