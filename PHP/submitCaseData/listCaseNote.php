<?php
function listCaseNote(){
  $returnString = '';
  $note = $_SESSION['complaint']->getData('caseNote');
  if($note !== false){
  	$returnString .= "<table class='complaintTable stackable'>";
  	$returnString .= "<thead><th>Case Note</th></thead>";
  	$returnString .= "<tbody>";
  	$returnString .= "<tr><td>Date</td><td><b>".$note['date']."</b></td></tr>";
  	$returnString .= "<tr><td colspan=2 style='width: 600px; padding: 10px 5px 10px 5px'>".$note['note']."</td></tr>";
  	$returnString .= "<tr><td>Taken By</td><td><b>".$note['author']."</b></td></tr>";
  	$returnString .= "</tbody>";
  	$returnString .= "</table>";
  }
  return $returnString;
}
?>
