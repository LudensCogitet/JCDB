<?php
function listCaseNotes($prefix, $caseNumber){
  $returnString = false;

  $caseNotes = grabCaseNotes($prefix,$caseNumber);
  if(count($caseNotes) > 0){

  $returnString = '<div style="display: none;" id="caseNoteTarget">';
      foreach($caseNotes as $note){
        $returnString .= "<div class='stackable'>";
        $returnString .= "<table class='complaintTable' id='".$note['rowID']."'>";
        $returnString .= "<thead>";
        if(isset($_SESSION['superuser'])){
          $returnString .= "<th>Case Note</th><th><div class='UIButton buttonShort deleteCaseNote' data-rowid='".$note['rowID']."'>Delete</div></th>";
        }
        else{
          $returnString .= "<th colspan=2>Case Note</th>";
        }
        $returnString .= "</th>";
        $returnString .= "</thead><tbody>";
        $returnString .= "<tr><td>Date</td><td><b>".$note['timeEntered']."</b></td></tr>";
        $returnString .= "<tr><td colspan=2 style='font-family: arial; min-width: 300px; padding: 10px 5px 10px 5px;'>".$note['note']."</td></tr>";
        $returnString .= "<tr><td>Taken by</td><td><b>".$note['author']."</b></td></tr>";
        $returnString .= "</tbody>";
        $returnString .= "</table>";

        $returnString .= "</div>";
      }
      $returnString .= '</div>';
    }
  return $returnString;
}
?>
