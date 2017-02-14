<html>
<head>
</head>
<body>
</body>
</html>

<?php
	require './NewComplaintData.php';
	session_start();
	
	if(isset($_SESSION['newComplaint'])){
		$complaint = $_SESSION['newComplaint'];
		echo $complaint->submitToDatabase();
		unset($_SESSION['newComplaint']);
	}
?>
<script>
localStorage.removeItem("lastFormData");
</script>

<form style='display:inline;' action='newComplaint.php'><input type='submit' value='Submit Another'></input></form>
<form style='display:inline;' action='index.html'><input type='submit' value='Back to Database'></input></form>