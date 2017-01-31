<html>
<head>
</head>
<body>
<?php
include 'ComplaintFormData.php';

if($_SERVER['REQUEST_METHOD'] === "POST"){
	$newForm = new ComplaintFormData();
	echo "<img src='".$newForm->getData('formScan')."'>";
	echo "Case Number: ".$newForm->getData('prefix').$newForm->getData('caseNumber')."<p>";
	foreach(ComplaintFormData::$multiFields as $field){
		echo "<h4>".$field."</h4>";
		echo $newForm->getData($field, "string");
	}	
	
}
?>
</body>
</html>