<html>
<head>
<script>
localStorage.removeItem("lastForm");
</script>
</head>
<body>
<?php 
require './ComplaintFormData.php';

session_start(); 

?>
	<h3><a href="newComplaint.php">Add Complaint</a></h3>
</body>
</html>