<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	require_once 'PHP/CaseData.php';
	require_once 'PHP/checkUserPermissions.php';
	require_once 'PHP/caseForm.php';
	require_once 'PHP/submitCaseData/checkRequests.php';
	require_once 'PHP/submitCaseData/listCaseNote.php';
	require_once 'PHP/submitCaseData/listContempt.php';

	session_start();
	checkUserPermissions('username');
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Submit case data</title>
		<link rel="stylesheet" type="text/css" href="CSS/submitCaseData.css">
		<link rel="stylesheet" type="text/css" href="CSS/ComplaintForm.css">
		<link rel="stylesheet" type="text/css" href="CSS/UI.css">
		<script src="JS/jquery-3.1.1.min.js"></script>
		<script src="JS/complaintForm.js"></script>
		<script src="JS/formatting.js"></script>
	</head>
	<body>
		<?php
			if(checkRequests())
				return;

				$_SESSION['complaint'] = new CaseData();
		?>
		<h1 style='border-bottom: 2px solid black'> Review Case Entry</h1>
		<?php
			if($_SESSION['complaint']->setToDelete() == true){
				echo "<h4 style='color: red'>This case and all related entries will be deleted!</h4>";
			}

			echo '<div id="tableTarget">';
				echo '<img id="formScan" src='.$_SESSION['complaint']->getData('formScan').'>';
				echo caseForm("cached",false);
			echo '</div>';
			echo listCaseNote();
			echo listContempt();
		?>
		<form name='submissionAction' method="POST">
			<button class='UIButton buttonMedium' type="submit" name="confirm">Confirm</button>
		</form>
		<div class='UIButton buttonMedium sideBySide' onclick="location.href='enterCaseData.php?modifyComplaint=true';">Modify</div>
		</div>
	</body>
</html>
