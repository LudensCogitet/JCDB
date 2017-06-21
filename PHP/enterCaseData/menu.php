<?php
  function menu($caseNotes = false, $contempts = false, $prefix = -1, $caseNumber = -1){
    if($prefix === -1 && $caseNumber === -1 && isset($_SESSION['complaint'])){
      if($_SESSION['complaint']->getData('caseNumber') !== -1){
        $prefix = $_SESSION['complaint']->getData('prefix');
        $caseNumber = $_SESSION['complaint']->getData('caseNumber');
      }
    }

    $menuString = '<div class="menu">';

    $menuString .= '<div class="UIButton buttonMedium moveRight forceStack" id="addContempt">Add Contempt</div>';

    if($contempts === true)
        $menuString .= '<div class="UIButton buttonMedium moveRight forceStack" id="showContempts">Show Contempt Charges</div>';

    if($caseNotes === true)
        $menuString .= '<div class="UIButton buttonMedium moveRight forceStack" id="showNotes">Show Case Notes</div>';

    $caseHistoryParams = "prefix=".$prefix."&caseNumber=".$caseNumber;
      $menuString .= "<div class='UIButton buttonMedium moveRight forceStack' onclick='window.open(\"/caseHistory.php?".$caseHistoryParams."\",\"_blank\");'>Printable Case Data</div>";

    if(isset($_SESSION['superuser'])){
        $menuString .= '<div class="UIButton buttonMedium danger moveRight forceStack" id="deleteButton" onclick="document.enterComplaintButton.deleteComplaint.click();">Delete Complaint</div>';
        $menuString .= '<input style="display: none;" name="deleteComplaint" type="submit"></input>';
    }

      $menuString .= '</div>';
      $menuString .= '<script src="JS/enterCaseData/jqueryFunctions.js"></script>';

    return $menuString;
  }
?>
