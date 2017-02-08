<html>
<head>
<link rel="stylesheet" type="text/css" href="./submitCaseData.css">
<link rel="stylesheet" type="text/css" href="./newComplaint.css">
<script src="jquery-3.1.1.min.js"></script>
<script src="ComplaintForm.js"></script>
<script>
$(document).ready(function(){
	console.log(localStorage.lastFormData);
	
	var complaintForm = new ComplaintForm(JSON.parse(localStorage.lastFormData),true);
	$("#tableTarget").append(complaintForm.getJqueryElement());
	
	$("#confirm").click(function(){
		if(localStorage.lastFormData)
			localStorage.removeItem("lastFormData");
		}
	);
});
</script>
</head>
<body>

<?php
require './ComplaintFormData.php';

session_start();

if($_SERVER['REQUEST_METHOD'] === "POST"){
	$newForm = new ComplaintFormData();
	$_SESSION['newComplaint'] = $newForm;
	echo "<img style='height:90%; float: right; border: 3px solid black; box-shadow: 5px 5px 5px black;' src='".$newForm->getData('formScan')."'>";
	
	/*echo "<span style='float: left; margin-right: 10%;'>";
	foreach(ComplaintFormData::$multiFields as $field){
		echo "<b>".$field.": </b>".$newForm->getData($field, "string")."<br>";
	}	
	echo "</span>";*/
}
?>
<div id="tableTarget"></div>
<form style="display:inline;" action='confirmSubmitCaseData.php'>
	<input type="submit" value="Confirm"></input>
</form>
<button onclick='history.go(-1);'>Modify</button>
</body>
</html>