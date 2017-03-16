<html>
<head>
<link rel="stylesheet" type="text/css" href="../CSS/submitCaseData.css">
<link rel="stylesheet" type="text/css" href="../CSS/ComplaintForm.css">
<script src="../JS/jquery-3.1.1.min.js"></script>
<script src="../JS/ComplaintForm.js"></script>
<?php
require './ComplaintData.php';

session_start();

if($_SERVER['REQUEST_METHOD'] === "POST"){
	if(!isset($_SESSION['complaint'])){
		$newForm = new ComplaintData();
		$_SESSION['complaint'] = $newForm;
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
			echo "var formScan = '<img src=\'".$newForm->getData('formScan')."\'>'";
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
<form style="display:inline;" method="POST">
	<input type="submit" value="Confirm"></input>
</form>
<button onclick='history.go(-1);'>Modify</button>
</body>
</html>
<?php
	}
	else{
		$complaint = $_SESSION['complaint'];
		echo $complaint->submitToDatabase();
		unset($_SESSION['complaint']);
?>
<script>
localStorage.removeItem("lastFormData");
</script>
<form style='display:inline;' action='./enterComplaintData.php'><input type='submit' value='Submit Another'></input></form>
<form style='display:inline;' action='../index.html'><input type='submit' value='Back to Database'></input></form>
<?php	
	}
}
?>