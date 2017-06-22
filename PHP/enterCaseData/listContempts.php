<?php
require_once 'PHP/caseDataFunctions.php';

function listContempts($prefix = -1, $caseNumber = -1){
  $returnString = false;

  if($prefix !== -1 && $caseNumber !== -1){
    $caseContempts = grabContempts($prefix,$caseNumber);
    $contemptStatus = grabContemptStatus($prefix,$caseNumber);

    if($caseContempts){
      $returnString = '<div style="display: none;" id="contemptTarget">';
        for($i = 0; $i < count($caseContempts); $i++){
          $returnString .= "<div class='stackable'>";
          $returnString .= "<table class='complaintTable' id='".$caseContempts[$i]['rowID']."'>";
          if(isset($_SESSION['superuser'])){
            $returnString .= "<thead><th>".ucfirst($caseContempts[$i]['charge'])."</th>";
            $returnString .= "<th style='text-align: right'><div class='UIButton buttonShort deleteContempt' data-entryrowid=".$caseContempts[$i]['rowID']." data-statusrowid=".$contemptStatus[$i]['rowID'].">Delete</div></th>";
          }
          else{
            $returnString .= "<thead><th colspan=2>".ucfirst($caseContempts[$i]['charge'])."</th>";
          }
          $returnString .= "</thead><tbody>";
          $returnString .= "<tr><td>Defendant</td><td><b>".$caseContempts[$i]['defendant']."</b></td></tr>";
          if(count($caseContempts[$i]['witness']) > 0){
              $returnString .= "<tr><td>Witnesses</td><td><b>".$caseContempts[$i]['witness']."</b></td></tr>";
          }
          $returnString .= "<tr><td>Date filed</td><td><b>".$caseContempts[$i]['dateOfIncident']."</b></td></tr>";
          $returnString .= "<tr><td>Status</td><td><b>".$contemptStatus[$i]['status']."</b></td></tr>";
          $returnString .= "</tbody>";
          $returnString .= "</table>";
          $returnString .= "</div>";
        }
      $returnString .= '</div>';
    }
  }
  return $returnString;
}
?>
