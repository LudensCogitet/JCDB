<?php 
require './ComplaintData.php';
session_start();

if(isset($_SESSION['username'])){
$newModify = false;
if(isset($_SESSION['complaint'])){
	if(isset($_GET['newComplaint'])){
		setcookie('complaintData',"",1);
	}
	else if(isset($_GET['modifyComplaint'])){
		if($_SESSION['complaint']->getData('caseNumber') != -1){
			if(isset($_SESSION['superuser'])){
				$formSettings = "'both',false,true,false";
			}
			else{
				$formSettings = "'both','top',true";
			}
		}
		else{
			$formSettings = "'top'";
			$newModify = true;
		}
		setcookie('complaintData',$_SESSION['complaint']->encodeData(),time()+10);
	}
	unset($_SESSION['complaint']);
}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="../CSS/UI.css">
  <link rel="stylesheet" type="text/css" href="../CSS/ComplaintForm.css">
  <script src="../JS/formatting.js"></script>
	<script src="../JS/jquery-3.1.1.min.js"></script>
	<script src="../JS/jquery.cookie.js"></script>
  <script src="../JS/ComplaintForm.js"></script>
  <script>
$(document).ready(function(){
<?php 
$submissionButtonName = "Submit Complaint";
if(isset($_GET['newComplaint'])){
	$scanReq = 'required';
	echo "complaintForm('#tableTarget');";
}
else if(isset($_GET['modifyComplaint'])){
	$scanReq = '';
	echo "complaintForm('#tableTarget',$.cookie('complaintData'),".$formSettings.");";
	$submissionButtonName = "Resubmit Complaint";
}
else if(isset($_GET['updateComplaint']) && isset($_GET['prefix']) && isset($_GET['caseNumber'])){
	$scanReq = '';
	if(isset($_SESSION['superuser'])){
		echo "complaintForm('#tableTarget',[".$_GET['prefix'].",".$_GET['caseNumber']."],'both',false,true,false);";
	}
	else{
		echo "complaintForm('#tableTarget',[".$_GET['prefix'].",".$_GET['caseNumber']."],'both','top',true);";
	}
	$submissionButtonName = "Update Complaint";
}
?>
});
</script>
</head>
<body>
<form id="complaintEntryForm" name="enterComplaintButton" action="submitComplaintData.php" method="POST" enctype="multipart/form-data">
	<?php if(isset($_GET['newComplaint']) || $newModify == true || isset($_SESSION['superuser'])){ ?>
	<div style="margin-left: 2px; margin-bottom: 5px; padding: 3px 0px 3px 3px; border: 2px solid black; width: 423px;">
		<h4 style="margin-top: 0px;">Complaint Form Scan<h4><p><input id="formScanInput" type="file" name="formScanFile" accept="image/jpeg" <?php echo $scanReq; ?>></input>
	<?php } ?>
	</div>
	<div id="tableTarget"></div>
	<input style="display: none;" name='submit' type="submit"></input>  
</form>
<div id="menu">
	<div class="UIButton buttonMedium" id="submissionButton" onclick="document.enterComplaintButton.submit.click();"><?php echo $submissionButtonName ?></div>
	<div class="UIButton buttonMedium" onclick="location.href='../index.php';">Back to Database</div>
</div>
</body>
</html>
<?php } ?>