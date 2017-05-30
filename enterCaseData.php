<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
require_once 'PHP/CaseData.php';
require_once 'PHP/displayCase.php';
session_start();

if(isset($_SESSION['username'])){
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

	if(isset($_GET['updateComplaint'])){
		$caseDoesExist = true;
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
else if(isset($_GET['updateComplaint']) && isset($_GET['prefix']) && isset($_GET['caseNumber'])){
	$scanReq = '';
	if(isset($_SESSION['superuser'])){
		echo "complaintForm('#complaintTarget',[".$_GET['prefix'].",".$_GET['caseNumber']."],false,true);";
	}
	else{
		echo "complaintForm('#complaintTarget',[".$_GET['prefix'].",".$_GET['caseNumber']."],true,true);";
	}
	$submissionButtonName = "Update Complaint";
}

if($caseDoesExist){
	?>
				$("#showNotes").click(function(){
					if($('#caseNoteTarget').is(':hidden')){
						$('#caseNoteTarget').show();
						$(this).text('Hide Case Notes');
					}
					else{
						$('#caseNoteTarget').hide();
						$(this).text('Show Case Notes');
					}
				});

				$(".deleteCaseNote").click(function(){
					$.ajax({url:"PHP/deleteCaseNote.php",
							type: "POST",
							data: {'rowID': $(this).data('rowid')},
							success: function(result){
								$('#caseNoteTarget').append("<div>lol</div>"); //find('#'+$(this).data('rowid')).remove();
							}
					});
				});
<?php
}
?>
});
</script>
</head>
<body>
<form id="complaintEntryForm" name="enterComplaintButton" action="submitCaseData.php" method="POST" enctype="multipart/form-data">
	<?php if(isset($_GET['newComplaint']) || $newModify == true || isset($_SESSION['superuser'])){ ?>
	<div style="margin-left: 2px; margin-bottom: 5px; padding: 3px 0px 3px 3px; border: 2px solid black; width: 423px;">
		<h4 style="margin-top: 0px;">Complaint Form Scan<h4><p><input id="formScanInput" type="file" name="formScanFile" accept="image/jpeg" <?php echo $scanReq; ?>></input>
	<?php } ?>
	</div>
	<div id="complaintTarget"></div>
	<?php if($caseDoesExist == true){ ?>
		<div style="display: none;" id="caseNoteTarget">
			<?php
				$caseNotes = grabCaseNotes($_GET['prefix'],$_GET['caseNumber']);
				foreach($caseNotes as $note){
					echo "<table class='complaintTable' id='".$note['rowID']."' style='margin-bottom: 20px;'>";
					echo "<thead><th>Case Note</th></thead>";
					echo "<tbody>";
					echo "<tr><td>Date</td><td><b>".$note['timeEntered']."</b></td></tr>";
					echo "<tr><td colspan=2 style='width: 600px'>".$note['note']."</td></tr>";
					echo "<tr><td>Taken By</td><td><b>".$note['author']."</b></td></tr>";
					echo "</tbody>";
					echo "</table>";
					if(isset($_SESSION['superuser'])){
						echo "<div class='UIButton buttonMedium deleteCaseNote' data-rowid='".$note['rowID']."'>Delete Note</div>";
					}
				}
			?>
		</div>
		<?php
			if(count($caseNotes) > 0){
				echo '<div class="UIButton buttonMedium" id="showNotes">Show Case Notes</div>';
			}

			echo "<table class='complaintTable'>";
			echo "<thead><th>New Case Note</th></thead>";
			echo "<tbody>";
			echo "<tr><td><b>Date</b></td><td><input readonly type='text' name='newCaseNoteDate' value='".Date('Y-m-d')."'></input></td></tr>";
			echo "<tr><td colspan=2 style='width: 600px; height: 300px;'><textarea name='newCaseNoteContent' style='width: 100%; height: 100%;'></textarea></td></tr>";
			echo "<tr><td><b>Taken By</b></td><td><input readonly type='text' name='newCaseNoteAuthor' value='".$_SESSION['username']."'></input></td></tr>";
			echo "</tbody>";
			echo "</table>";

		}
		?>
	<input style="display: none;" name='submit' type="submit"></input>
	<?php
	if($deleteOption == true){
		echo '<input style="display: none;" name="deleteComplaint" type="submit"></input>';
	}
	?>
</form>
<div id="menu">
	<div class="UIButton buttonMedium" id="submissionButton" onclick="document.enterComplaintButton.submit.click();"><?php echo $submissionButtonName ?></div>
	<?php if($deleteOption == true){
					echo '<div style="float: right" class="UIButton buttonMedium" id="deleteButton" onclick="document.enterComplaintButton.deleteComplaint.click();">Delete Complaint</div>';
				}
	?>
	<div class="UIButton buttonMedium" onclick="location.href='index.php';">Back to Database</div>
</div>
</body>
</html>
<?php } ?>
