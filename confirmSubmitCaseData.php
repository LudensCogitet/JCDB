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
		echo "<button onclick='window.open(\"./newComplaint.php\")'>Submit another</button>";
		echo "<button onclick='window.open(\"./index.php\")'>Main Menu</button";
	}
?>