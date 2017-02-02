<html>
<head>
</head>
<body>
</body>
</html>

<?php
	require './ComplaintFormData.php';
	session_start();
	
	if(isset($_SESSION['newComplaint'])){
		$complaint = $_SESSION['newComplaint'];
		$caseNum = $complaint->submitToDatabase();
		echo "Case #".$caseNum." added to database.<p>";
		unset($_SESSION['newComplaint']);
	}
?>
<script>
localStorage.removeItem("lastForm");
localStorage.removeItem("lastFormValues");
</script>

<form style='display:inline;' action='newComplaint.php'><input type='submit' value='Submit Another'></input></form>
<form style='display:inline;' action='index.php'><input type='submit' value='Main Menu'></input></form>