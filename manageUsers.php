<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	session_start();

	require_once 'PHP/checkUserPermissions.php';
	checkUserPermissions('superuser');

	require_once 'PHP/databaseConnect.php';
	require_once 'PHP/manageUsers/checkRequests.php';
	require_once 'PHP/manageUsers/getUserList.php';

	$dbConn = databaseConnect('write');
	checkRequests($dbConn);
?>
<html>
	<head>
		<link rel="stylesheet" type="text/css" href="CSS/UI.css">
		<link rel="stylesheet" type="text/css" href="CSS/usersDisplay.css">
	</head>
	<body>
		<div class="centerBox" style="width:300px;">
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
			<?php getUserList($dbConn); ?>
			</div>
		</div>
	</body>
</html>
