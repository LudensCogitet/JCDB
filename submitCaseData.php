<html>
<head>
</head>
<body>
<?php
include 'ComplaintFormData.php';

if($_SERVER['REQUEST_METHOD'] === "POST"){
	$newForm = new ComplaintFormData();
	
	foreach(ComplaintFormData::$multiFields as $field){
		echo "<h4>".$field."</h4>";
		$contents = $newForm->getData($field);
		foreach($contents as $content){
			echo $content."<br>";
		}
	}
}
?>
</body>
</html>