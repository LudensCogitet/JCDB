<?php
  function updateCaseButton(){
    if(isset($_SESSION['username'])){
      echo '<span id="updateCasebutton">';
      echo '<form id="updateCaseForm" name="updateCaseForm" method="GET" target="_blank" action="enterCaseData.php"><input style="display: none;" type="submit" name="updateCase"></input></form>';
      echo '<div style="float:left" class="UIButton buttonLong" onclick="document.updateCaseForm.updateCase.click();">Update Complaint</div>';
      echo '</span>';
    }
  }
?>
