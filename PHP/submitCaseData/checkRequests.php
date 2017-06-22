<?php
function checkRequests(){
  $confirm = isset($_POST['confirm']);
  if($confirm){
    echo '<div class="centerBox">';
    echo '<div class="noteBox">'.$_SESSION['complaint']->submitToDatabase().'</div><br>';
    echo '<div class="UIButton buttonMedium sideBySide" onclick="location.href=\'enterCaseData.php?newComplaint=true\';">Submit A New Complaint</div><br>';
    echo '<div class="UIButton buttonMedium sideBySide" onclick="location.href=\'index.php\';">Return To Database</div>';
    echo '</div>';
    echo '</body>';
    echo '</html>';

    unset($_SESSION['complaint']);
  }
  return $confirm;
}
?>
