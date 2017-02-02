<html>
<head>
<link rel="stylesheet" type="text/css" href="./submitCaseData.css">
<script src="jquery-3.1.1.min.js"></script>
<script>
$(document).ready(function(){
	console.log(localStorage.lastForm);
	
	$("#confirm").click(function(){
		if(localStorage.lastForm)
			localStorage.removeItem("lastForm");
			localStorage.removeItem("lastFormValues");
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
	echo "<table>";
	echo "<tr><td>Plaintiff(s)</td><td class='result'>".$newForm->getData("plaintiff","string")."</td><td>Date of Incident</td><td class='result'>".$newForm->getData("dateOfIncident","string")."</td></tr>";
	echo "<tr><td>Defendant(s)</td><td class='result'>".$newForm->getData("defendant","string")."</td><td>Time of Incident</td><td class='result'>".$newForm->getData("timeOfIncident","string")."</td></tr>";
	echo "<tr><td>Witness(es)</td><td class='result'>".$newForm->getData("witness","string")."</td><td>Location</td><td class='result'>".$newForm->getData("location","string")."</td></tr>";
	echo "<tr><td>What Happened</td><td class='result'>".$newForm->getData("whatHappened")."</td><td>Charges</td><td class='result'>".$newForm->getData("charge","string")."</td></tr>";
	echo "</table>";
	
	/*echo "<span style='float: left; margin-right: 10%;'>";
	foreach(ComplaintFormData::$multiFields as $field){
		echo "<b>".$field.": </b>".$newForm->getData($field, "string")."<br>";
	}	
	echo "</span>";*/
}
?>
<form style="display:inline;" action='confirmSubmitCaseData.php'>
	<input type="submit" value="Confirm"></input>
</form>
<button onclick='history.go(-1);'>Modify</button>
</body>
</html>