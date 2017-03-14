<html>
<head>
  <link rel="stylesheet" type="text/css" href="ComplaintForm.css">
  <script src="jquery-3.1.1.min.js"></script>
  <script src="ComplaintForm.js"></script>
  <script>
	$(document).ready(function(){
	  var complaintForm = null;
	  if(localStorage.lastFormData){
			complaintForm = new ComplaintForm(JSON.parse(localStorage.lastFormData));
			if(complaintForm.getData("formScan") != ""){
				$("#formScanInput").removeAttr("required");
			}
			localStorage.removeItem("lastFormData");
	  }
	  else{
		complaintForm = new ComplaintForm();
	  }
	  
	  $("#tableTarget").append(complaintForm.getJqueryElement());
		   
	 $("#complaintEntryForm").submit(function(){
	  complaintForm.updateFromJquery();
	  localStorage.setItem("lastFormData",JSON.stringify(complaintForm.getData()));
  });
});
</script>
</head>
<body>
<?php 
require './NewComplaintData.php';
session_start();

if(isset($_SESSION['newComplaint'])){
	unlink($_SESSION['newComplaint']->getData('formScan'));
	unset($_SESSION['newComplaint']);
}

?>
<form id="complaintEntryForm" action="submitCaseData.php" method="POST" enctype="multipart/form-data">
  <div style="margin-left: 2px; margin-bottom: 5px; padding: 3px 0px 3px 3px; border: 2px solid black; width: 423px;">
  <h4 style="margin-top: 0px;">Complaint Form Scan<h4><p><input id="formScanInput" type="file" name="formScanFile" required></input>
  </div>
  <div id="tableTarget"></div>
<input type="submit" name="newComplaint"></input>  
</form>
<button onclick="history.go(-1)">Back to Database</button>
</body>
</html>