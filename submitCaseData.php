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
<div id="tableTarget"></div>
<form name='submissionAction' style="display: none;" method="POST">
<input type="submit" name="confirm"></input>
</form>
<div class='UIButton buttonMedium' onclick='document.submissionAction.confirm.click()'>Confirm</div>
<div class='UIButton buttonMedium' onclick="location.href='enterCaseData.php?modifyComplaint=true';">Modify</div>
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
<div class='UIButton buttonMedium' onclick="location.href='enterCaseData.php?newComplaint=true';">Submit A New Complaint</div><br>
<div class='UIButton buttonMedium' onclick="location.href='index.php';">Return To Database</div>
</div>
</body>
</html>
<?php
		}
	}
?>
