<?php
function newCaseNote(){
  $returnString = false;

  if(isset($_SESSION['username'])){
    $returnString = "<div class='stackable'>";
    $returnString .= "<table class='complaintTable'>";
    $returnString .= "<thead><th colspan=2>New Case Note</th></thead>";
    $returnString .= "<tbody>";
    $returnString .= "<tr><td><b>Date</b></td><td><input readonly type='text' name='newCaseNoteDate' value='".Date('Y-m-d')."'></input></td></tr>";
    $returnString .= "<tr><td colspan=2 style='width: 600px; height: 400px;'><textarea name='newCaseNoteContent' style='width: 100%; height: 100%;'></textarea></td></tr>";
    $returnString .= "<tr><td><b>Taken by</b></td><td><input readonly type='text' name='newCaseNoteAuthor' value='".$_SESSION['username']."'></input></td></tr>";
    $returnString .= "</tbody>";
    $returnString .= "</table>";
    $returnString .= "</div>";
  }

  return $returnString;
}
?>
