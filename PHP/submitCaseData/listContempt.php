<?php
function listContempt(){
  $returnString = '';

  $contempt = $_SESSION['complaint']->getData('contempt');

  if($contempt !== false){
  	$returnString .= "<table class='complaintTable stackable'>";
  	$returnString .= "<thead><th>Contempt Charge</th><th style='border: none;'></thead>";
  	$returnString .= "<tbody>";
  	$returnString .= "<tr><td><b>Plaintiff</b></td><td>".$contempt['plaintiff']."</td></tr>";
  	$returnString .= "<tr><td><b>Defendant</b></td><td>".$contempt['defendant']."</td></tr>";
  	$returnString .= "<tr><td><b>Charge</b></td><td>".$contempt['charge']."</td></tr>";
  	$returnString .= "<tr><td><b>Date</b></td><td>".$contempt['date']."</td></tr>";
  	$returnString .= "</tbody>";
  	$returnString .= "</table>";
  }
  return $returnString;
}
?>
