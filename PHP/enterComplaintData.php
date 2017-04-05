<?php 
require './ComplaintData.php';
session_start();

if(isset($_SESSION['complaint'])){
	unlink($_SESSION['complaint']->getData('formScan'));
	unset($_SESSION['complaint']);
}
?>
<html>
<head>
	<link rel="stylesheet" type="text/css" href="../CSS/UI.css">
  <link rel="stylesheet" type="text/css" href="../CSS/ComplaintForm.css">
  <script src="../JS/formatting.js"></script>
	<script src="../JS/jquery-3.1.1.min.js"></script>
  <script src="../JS/ComplaintForm.js"></script>
  <script>
	var complaintForm = null;
	function saveComplaintForm(){
		if(complaintForm != null){
			complaintForm.updateFromJquery();
			localStorage.setItem("lastFormData",JSON.stringify(complaintForm.getData()));
		}
	}
<?php if(isset($_SESSION['superuser'])){ ?>
	function deleteComplaint(){
		$("#complaintEntryForm").append("<input type='hidden' name='deleteComplaint'></input>");
		saveComplaintForm();
		document.enterComplaintButton.submit();
	}
<?php } ?>
	$(document).ready(function(){
	  
	  var element;
		if(localStorage.lastFormData){
			complaintForm = new ComplaintForm(JSON.parse(localStorage.lastFormData));
			if(complaintForm.getData("formScan") != ""){
				$("#formScanInput").removeAttr("required");
			}
			if(complaintForm.getData("caseNumber") != ""){
<?php if(isset($_SESSION['superuser'])){ ?>	
				$("#menu").append("<div class='UIButton buttonMedium' onclick='deleteComplaint();' style='float: right;'>Delete Complaint</div>");
<?php } ?>
				element = complaintForm.getJqueryElement("complete")
			}
			else{
				element = complaintForm.getJqueryElement();
			}
			localStorage.removeItem("lastFormData");
		}
	  else{
			complaintForm = new ComplaintForm();
			element = complaintForm.getJqueryElement();
			$("#submissionButton").text("Add Complaint");
	  }
		
		$("#tableTarget").append(element);
	
		$("#complaintEntryForm").submit(saveComplaintForm);
});
</script>
</head>
<body>
<form id="complaintEntryForm" name="enterComplaintButton" action="submitComplaintData.php" method="POST" enctype="multipart/form-data">
	<div style="margin-left: 2px; margin-bottom: 5px; padding: 3px 0px 3px 3px; border: 2px solid black; width: 423px;">
		<h4 style="margin-top: 0px;">Complaint Form Scan<h4><p><input id="formScanInput" type="file" name="formScanFile" required></input>
	</div>
	<div id="tableTarget"></div>
	<input style="display: none;" type="submit"></input>  
</form>
<div id="menu">
	<div class="UIButton buttonMedium" id="submissionButton" onclick="saveComplaintForm();document.enterComplaintButton.submit();">Update Complaint</div>
	<div class="UIButton buttonMedium" onclick="location.href='../index.php';">Back to Database</div>
</div>
</body>
</html>