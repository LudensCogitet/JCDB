<html>
<head>
</head>
<body>
<?php
  if($_SERVER['REQUEST_METHOD'] === "POST"){
	move_uploaded_file($_FILES['caseScan']['tmp_name'],"images/new.jpg");
	echo "<img src='images/new.jpg' style='width:100%;'>";
  }
?>
</body>
</html>