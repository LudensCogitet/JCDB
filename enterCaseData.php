<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	require_once 'PHP/CaseData.php';
	require_once 'PHP/checkUserPermissions.php';
	require_once 'PHP/caseForm.php';
	require_once 'PHP/enterCaseData/menu.php';
	require_once 'PHP/enterCaseData/scanUpload.php';
	require_once 'PHP/enterCaseData/listContempts.php';
	require_once 'PHP/enterCaseData/listCaseNotes.php';
	require_once 'PHP/enterCaseData/newCaseNote.php';

	session_start();

	checkUserPermissions('username');

	$menu = '';
	$caseForm = '';
	$submissionButtonName = '';

	$prefix = -1;
	$caseNumber = -1;
	$contempts = false;
	$caseNotes = false;
	$newCaseNote = false;

	if(isset($_GET['newComplaint'])){
		$caseForm = caseForm('new');
		$submissionButtonName = 'Submit';
	}
	else{
		if(isset($_GET['modifyComplaint'])){
				if($_SESSION['complaint']->getData('caseNumber') != -1){
					$prefix = $_SESSION['complaint']->getData('prefix');
					$caseNumber = $_SESSION['complaint']->getData('caseNumber');
					$newCaseNote = newCaseNote();
				}
			$caseForm = caseForm('cached', false);
			$submissionButtonName = 'Resubmit';
		}
		else if(isset($_GET['updateCase'])){
			$prefix = $_GET['prefix'];
			$caseNumber = $_GET['caseNumber'];

			$caseForm = caseForm('existing',false,$prefix,$caseNumber);
			$submissionButtonName = 'Update';
			$newCaseNote = newCaseNote();
		}

		$contempts = listContempts($prefix,$caseNumber);
		$caseNotes = listCaseNotes($prefix,$caseNumber);

		$printContempt = $contempts === false ? false : true;
		$printCaseNotes = $caseNotes === false ? false : true;

		$menu = menu($caseNotes !== false, $contempts !== false, $prefix, $caseNumber);
	}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/UI.css">
  <link rel="stylesheet" type="text/css" href="CSS/ComplaintForm.css">
  <script src="JS/formatting.js"></script>
	<script src="JS/jquery-3.1.1.min.js"></script>
</head>
<body>
<form id="complaintEntryForm" name="enterComplaintButton" action="submitCaseData.php" method="POST" enctype="multipart/form-data">
	<?php
		echo $menu;
		echo scanUpload();
	?>
	<div id="complaintTarget"><?php echo $caseForm ?></div>
		<div id="newContemptTarget"></div>
			<?php
				echo $contempts;
		 		echo $caseNotes;
				echo $newCaseNote;
			?>
	<button class="UIButton buttonMedium" id="submissionButton" name="<?php $submissionButtonName ?>" type="submit"><?php echo $submissionButtonName; ?> Case</button>
</form>
</body>
</html>
