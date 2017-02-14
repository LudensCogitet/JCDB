<html>
<head>
<link rel="stylesheet" type="text/css" href="./submitCaseData.css">
<link rel="stylesheet" type="text/css" href="./ComplaintForm.css">
<script src="jquery-3.1.1.min.js"></script>
<script src="ComplaintForm.js"></script>
<?php
require './NewComplaintData.php';

session_start();

if($_SERVER['REQUEST_METHOD'] === "POST"){
	$newForm = new NewComplaintData();
	$_SESSION['newComplaint'] = $newForm;
}
?>
<script>
$(document).ready(function(){
	console.log(localStorage.lastFormData);
	
	var complaintForm = new ComplaintForm(JSON.parse(localStorage.lastFormData),"both");
	
	var display = "simple";
	if(complaintForm.getData("hearingNotes") != "")
		display = "complete";
		
	$("#tableTarget").append(complaintForm.getJqueryElement(display));
	
	<?php
		if(isset($_POST['newComplaint'])){
			echo "var formScan = '<img src=\'".$newForm->getData('formScan')."\'>'";
		}
		else{
			echo "var formScan = complaintForm.getData('formScan');";
		}
	?>
	
	formScan = $(formScan);
	formScan.css({
		"height": "90%",
		"float": "right",
		"border": "3px solid black",
	"box-shadow": "5px 5px 5px black"});
	$("#tableTarget").before(formScan);
	
	
	$("#confirm").click(function(){
		if(localStorage.lastFormData)
			localStorage.removeItem("lastFormData");
		}
	);
});
</script>
</head>
<body>
<div id="tableTarget"></div>
<form style="display:inline;" action='confirmSubmitCaseData.php'>
	<input type="submit" value="Confirm"></input>
</form>
<button onclick='history.go(-1);'>Modify</button>
</body>
</html>