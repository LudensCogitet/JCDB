<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
require_once 'PHP/CaseData.php';
require_once 'PHP/displayCase.php';
require_once 'PHP/checkUserPermissions.php';
session_start();

checkUserPermissions('username');

$deleteOption = false;
$newModify = false;
$caseDoesExist = false;
if(isset($_SESSION['complaint'])){
	if(isset($_GET['newComplaint'])){
		setcookie('CaseData',"",1);
	}
	else
	{
		if(isset($_GET['modifyComplaint'])){
			if($_SESSION['complaint']->getData('caseNumber') != -1){
				$caseDoesExist = true;
				$caseNotes = grabCaseNotes($_SESSION['complaint']->getData('prefix'),$_SESSION['complaint']->getData('caseNumber'));
				$caseInfo = grabCase($_SESSION['complaint']->getData('prefix'),$_SESSION['complaint']->getData('caseNumber'));

				if(isset($_SESSION['superuser'])){
					$deleteOption = true;
					$formSettings = "false,true";
				}
				else{
					$formSettings = "true,true";
				}
			}
			else{
				$formSettings = "'top'";
				$newModify = true;
			}
			setcookie('CaseData',$_SESSION['complaint']->encodeData(),time()+10);
		}
	}
	unset($_SESSION['complaint']);
}

	if(isset($_GET['updateCase'])){
		$caseDoesExist = true;
		$caseNotes = grabCaseNotes($_GET['prefix'],$_GET['caseNumber']);
		$caseInfo = grabCase($_GET['prefix'],$_GET['caseNumber']);

		if(isset($_SESSION['superuser'])){
			$deleteOption = true;
		}
	}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="CSS/UI.css">
  <link rel="stylesheet" type="text/css" href="CSS/ComplaintForm.css">
  <script src="JS/formatting.js"></script>
	<script src="JS/jquery-3.1.1.min.js"></script>
	<script src="JS/jquery.cookie.js"></script>
  <script src="JS/ComplaintForm.js"></script>
  <script>
$(document).ready(function(){
<?php
$submissionButtonName = "Submit Complaint";
if(isset($_GET['newComplaint'])){
	$scanReq = 'required';
	echo "complaintForm('#complaintTarget');";
}
else if(isset($_GET['modifyComplaint'])){
	$scanReq = '';
	echo "complaintForm('#complaintTarget',$.cookie('CaseData'),".$formSettings.");";
	$submissionButtonName = "Resubmit Complaint";
}
else if(isset($_GET['updateCase']) && isset($_GET['prefix']) && isset($_GET['caseNumber'])){
	$scanReq = '';
	if(isset($_SESSION['superuser'])){
		echo "complaintForm('#complaintTarget',[".$_GET['prefix'].",".$_GET['caseNumber']."],false,true);";
	}
	else{
		echo "complaintForm('#complaintTarget',[".$_GET['prefix'].",".$_GET['caseNumber']."],true,true);";
	}
	$submissionButtonName = "Update Complaint";
}
?>
});
</script>
<?php
	if($caseDoesExist){
		echo "<script src='JS/enterCaseDataJqueryFunctions.js'></script>";
	}
?>
</head>
<body>
	<?php
	  if($caseDoesExist){
			echo '<div class="menu">';

			echo '<div style="inline-block" class="sideBySide moveRight">';
			echo '<div class="UIButton buttonMedium" id="addContempt">Add Contempt</div>';
			echo "<div class='UIButton buttonMedium' onclick='document.caseHistoryButton.submit();'>Printable Case Data</div>";
			if($deleteOption == true){
				echo '<div class="UIButton buttonMedium danger" id="deleteButton" onclick="document.enterComplaintButton.deleteComplaint.click();">Delete Complaint</div>';
			}
			echo "</div>";

			echo '<div style="inline-block" class="sideBySide moveRight">';
			if(count($caseInfo) > 1)
				echo '<div class="UIButton buttonMedium" id="showContempts">Show Contempt Charges</div>';

			if(count($caseNotes) > 0)
				echo '<div class="UIButton buttonMedium" id="showNotes">Show Case Notes</div>';
			echo '</div>';

			echo "<form style='display: none;' method='POST' name='caseHistoryButton' action='PHP/caseHistory.php' target='_blank'>";
			echo "<input type='hidden' name='prefix' value='".$caseInfo[0]['prefix']."'></input>";
			echo "<input type='hidden' name='caseNumber' value='".$caseInfo[0]['caseNumber']."'></input>";
			echo "</form>";
			echo '</div>';
		}
	?>
</div>
</div>
<form id="complaintEntryForm" name="enterComplaintButton" action="submitCaseData.php" method="POST" enctype="multipart/form-data">
	<?php if(isset($_GET['newComplaint']) || $newModify == true || isset($_SESSION['superuser'])){ ?>
	<div style="margin-bottom: 5px; border: 2px solid black; width: 300px; padding-left: 5px;">
		<h4 style="margin-top: 0px;">Complaint Form Scan<h4><p><input id="formScanInput" type="file" name="formScanFile" accept="image/jpeg" <?php echo $scanReq; ?>></input>
	<?php } ?>
	</div>
	<div id="complaintTarget"></div>
	<?php if($caseDoesExist == true){
		echo '<div id="newContemptTarget">';
		echo '</div>';
		?>
		<div style="display: none;" id="contemptTarget">
		<?php
			$contemptStatus = grabContemptStatus($caseInfo[0]['prefix'],$caseInfo[0]['caseNumber']);
			for($i = 1; $i < count($caseInfo); $i++){
				echo "<div class='stackable'>";
				echo "<table class='complaintTable' id='".$caseInfo[$i]['rowID']."'>";
				if(isset($_SESSION['superuser'])){
					echo "<thead><th>".ucfirst($caseInfo[$i]['charge'])."</th>";
					echo "<th style='text-align: right'><div class='UIButton buttonShort deleteContempt' data-entryrowid=".$caseInfo[$i]['rowID']." data-statusrowid=".$contemptStatus[$i-1]['rowID'].">Delete</div></th>";
				}
				else{
					echo "<thead><th colspan=2>".ucfirst($caseInfo[$i]['charge'])."</th>";
				}
				echo "</thead><tbody>";
				echo "<tr><td>Defendant</td><td><b>".$caseInfo[$i]['defendant']."</b></td></tr>";
				if(count($caseInfo[$i]['witness']) > 0){
						echo "<tr><td>Witnesses</td><td><b>".$caseInfo[$i]['witness']."</b></td></tr>";
				}
				echo "<tr><td>Date filed</td><td><b>".$caseInfo[$i]['dateOfIncident']."</b></td></tr>";
				echo "<tr><td>Status</td><td><b>".$contemptStatus[$i-1]['status']."</b></td></tr>";
				echo "</tbody>";
				echo "</table>";
				echo "</div>";
			}
		?>
	</div>
		<div style="display: none;" id="caseNoteTarget">
			<?php
				foreach($caseNotes as $note){
					echo "<div class='stackable'>";
					echo "<table class='complaintTable' id='".$note['rowID']."'>";
					echo "<thead>";
					if(isset($_SESSION['superuser'])){
						echo "<th>Case Note</th><th><div class='UIButton buttonShort deleteCaseNote' data-rowid='".$note['rowID']."'>Delete</div></th>";
					}
					else{
						echo "<th colspan=2>Case Note</th>";
					}
					echo "</th>";
					echo "</thead><tbody>";
					echo "<tr><td>Date</td><td><b>".$note['timeEntered']."</b></td></tr>";
					echo "<tr><td colspan=2 style='font-family: arial; min-width: 300px; padding: 5px;'>".$note['note']."</td></tr>";
					echo "<tr><td>Taken by</td><td><b>".$note['author']."</b></td></tr>";
					echo "</tbody>";
					echo "</table>";

					echo "</div>";
				}
			?>
		</div>
		<?php
			echo "<div class='stackable'>";
			echo "<table class='complaintTable'>";
			echo "<thead><th colspan=2>New Case Note</th></thead>";
			echo "<tbody>";
			echo "<tr><td><b>Date</b></td><td><input readonly type='text' name='newCaseNoteDate' value='".Date('Y-m-d')."'></input></td></tr>";
			echo "<tr><td colspan=2 style='width: 600px; height: 400px;'><textarea name='newCaseNoteContent' style='width: 100%; height: 100%;'></textarea></td></tr>";
			echo "<tr><td><b>Taken by</b></td><td><input readonly type='text' name='newCaseNoteAuthor' value='".$_SESSION['username']."'></input></td></tr>";
			echo "</tbody>";
			echo "</table>";
			echo "</div>";
		}
		?>
	<input style="display: none;" name='submit' type="submit"></input>
	<?php
	if($deleteOption == true){
		echo '<input style="display: none;" name="deleteComplaint" type="submit"></input>';
	}
	?>
</form>
<div class="UIButton buttonMedium" id="submissionButton" onclick="document.enterComplaintButton.submit.click();"><?php echo $submissionButtonName ?></div>
</body>
</html>
