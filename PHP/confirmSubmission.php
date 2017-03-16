<html>
<head>
</head>
<body>
</body>
</html>

<?php
	require './ComplaintData.php';
	session_start();
	
	if(isset($_SESSION['complaint'])){
		$complaint = $_SESSION['complaint'];
	
		echo "<script>console.log(".$complaint->getData("formScan").");</script>";
		echo $complaint->submitToDatabase();
		unset($_SESSION['complaint']);
	}
?>
<script>
localStorage.removeItem("lastFormData");
</script>

<form style='display:inline;' action='./complaint.php'><input type='submit' value='Submit Another'></input></form>
<form style='display:inline;' action='../index.html'><input type='submit' value='Back to Database'></input></form>