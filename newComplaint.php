<html>
<head>
  <link rel="stylesheet" type="text/css" href="newComplaint.css">
  <script src="jquery-3.1.1.min.js"></script>
  <script src="newComplaint.js"></script>
  <script>
	$(document).ready(function(){
	  if(localStorage.lastForm){
		console.log(localStorage.lastForm);
		$("#formContainer").empty();
		$("#formContainer").append(JSON.parse(localStorage.lastForm));
		
		var savedValues = JSON.parse(localStorage.lastFormValues);
		
		console.log(savedValues);
		
		var formEls = document.getElementById("complaintEntryForm").elements;
		
		for(var i = 1; i < formEls.length; i++){
			if(formEls[i].type != "submit")
				formEls[i].value = savedValues[i];
		}
		
		localStorage.removeItem("lastForm");
		localStorage.removeItem("lastFormValues");
	}
	else{
		console.log();
	}
	
	 $("input[type='text']").keydown(reproduceField);
	   
	 $("#complaintEntryForm").submit(function(){
	  var formEls = document.getElementById("complaintEntryForm").elements;
	  var toStore = [];
	  
	  for(var i = 0; i < formEls.length; i++){
		  toStore.push(formEls[i].value);
	  }
	  localStorage.setItem("lastFormValues",JSON.stringify(toStore));
	  localStorage.setItem("lastForm",JSON.stringify($("#formContainer").html()));
  });
});
</script>
</head>
<body>
<?php 
require './ComplaintFormData.php';
session_start();

if(isset($_SESSION['newComplaint'])){
	unlink($_SESSION['newComplaint']->getData('formScan'));
	unset($_SESSION['newComplaint']);
}

?>
<div id="formContainer">
<form id="complaintEntryForm" action="submitCaseData.php" method="POST" enctype="multipart/form-data">
  <div style="margin-left: 2px; margin-bottom: 5px; padding: 3px 0px 3px 3px; border: 2px solid black; width: 423px;">
  <h4 style="margin-top: 0px;">Complaint Form Scan<h4><p><input type="file" name="formScan" required></input>
  </div>
  <table style="float: left;">
    <tr>
      <td>Plaintiff</td>
      <td><input type="text" name="plaintiff-1" data-repro="false" required></input></td>
    </tr>
    <tr>
      <td>Defendant</td>
      <td><input type="text" name="defendant-1" data-repro="false" required></input></td>
    </tr>
    <tr>      
      <td>Witness</td>
      <td><input type="text" name="witness-1" data-repro="false"></input></td>
    </tr>
    <tr>
      <td>What happened</td>
      <td><textarea name="whatHappened" required></textarea></td>
    </tr>
    <tr>
      <td style="border: none; text-align: left; padding-left: 0px;"><input type="submit" name="newComplaint"></input></td>
    </tr>
  </table>
<table>    
  <tr>
    <td>Date of Incident (YYYY-MM-DD)</td>
    <td><input type="text" name="dateOfIncident-1" data-repro="false" required></input></td>
  </tr>
  <tr>      
    <td>Time of Incident</td>
    <td><input type="text" name="timeOfIncident-1" data-repro="false" required></input></td>
  </tr>
  <tr>
    <td>Location</td>
    <td><input type="text" name="location-1" data-repro="false" required></input></td>
  </tr>
<tr>
  <td>Charge & Sec. Number</td><td><input type="text" name="charge-1" data-repro="false" required></input></td>
</tr>
</table>
</form>
</div>
</body>