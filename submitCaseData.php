<html>
<head>
<link rel="stylesheet" type="text/css" href="CSS/submitCaseData.css">
<link rel="stylesheet" type="text/css" href="CSS/ComplaintForm.css">
<link rel="stylesheet" type="text/css" href="CSS/UI.css">
<script src="JS/jquery-3.1.1.min.js"></script>
<script src="JS/jquery.cookie.js"></script>
<script src="JS/ComplaintForm.js"></script>
<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
require_once 'PHP/CaseData.php';

session_start();
if(isset($_SESSION['username'])){

	if(!isset($_SESSION['complaint'])){
		$_SESSION['complaint'] = new CaseData();
		setcookie('CaseData',$_SESSION['complaint']->encodeData(),time()+2);
		setcookie('formScan',$_SESSION['complaint']->getData('formScan'),time()+2);
?>
<script>
$(document).ready(function(){
	complaintForm('#tableTarget',$.cookie('CaseData'),true,false);

	$("#tableTarget").before($("<img id='formScan' src='"+$.cookie('formScan')+"'>"));
});
</script>
</head>
<body>
<h1 style='border-bottom: 2px solid black'> Review Case Entry</h1>
<?php
if($_SESSION['complaint']->setToDelete() == true){
	echo "<h4 style='color: red'>This case and all related entries will be deleted!</h4>";
}
?>
<div id="tableTarget"></div>
<?php
$note = $_SESSION['complaint']->getData('caseNote');
$contempt = $_SESSION['complaint']->getData('contempt');
if($note !== false){
	echo "<table class='complaintTable stackable'>";
	echo "<thead><th>Case Note</th></thead>";
	echo "<tbody>";
	echo "<tr><td>Date</td><td><b>".$note['date']."</b></td></tr>";
	echo "<tr><td colspan=2 style='width: 600px'>".$note['note']."</td></tr>";
	echo "<tr><td>Taken By</td><td><b>".$note['author']."</b></td></tr>";
	echo "</tbody>";
	echo "</table>";
}

if($contempt !== false){
	echo "<table class='complaintTable stackable'>";
	echo "<thead><th>Contempt Charge</th><th style='border: none;'></thead>";
	echo "<tbody>";
	echo "<tr><td><b>Plaintiff</b></td><td>".$contempt['plaintiff']."</td></tr>";
	echo "<tr><td><b>Defendant</b></td><td>".$contempt['defendant']."</td></tr>";
	echo "<tr><td><b>Charge</b></td><td>".$contempt['charge']."</td></tr>";
	echo "<tr><td><b>Date</b></td><td>".$contempt['date']."</td></tr>";
	echo "</tbody>";
	echo "</table>";
}
?>
<form name='submissionAction' style="display: none;" method="POST">
<input type="submit" name="confirm"></input>
</form>
<div style='margin-top: 5px;'>
<div class='UIButton buttonMedium sideBySide' onclick='document.submissionAction.confirm.click()'>Confirm</div>
<div class='UIButton buttonMedium sideBySide' onclick="location.href='enterCaseData.php?modifyComplaint=true';">Modify</div>
</div>
</body>
</html>
<?php
	}
	else{
?>
</head>
<body>
<div class='centerBox'>
<div class='noteBox'><?php echo $_SESSION['complaint']->submitToDatabase(); ?></div><br>

<?php
		unset($_SESSION['complaint']);
?>
<div class='UIButton buttonMedium sideBySide' onclick="location.href='enterCaseData.php?newComplaint=true';">Submit A New Complaint</div><br>
<div class='UIButton buttonMedium sideBySide' onclick="location.href='index.php';">Return To Database</div>
</div>
</body>
</html>
<?php
		}
	}
?>
