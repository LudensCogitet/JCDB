<?php
  function scanUpload(){
    $newModify = false;
    $returnString = false;

    if(isset($_SESSION['complaint'])){
      if($_SESSION['complaint']->getData('caseNumber') === -1)
        $newModify = true;
    }

    if(isset($_GET['newComplaint']) || $newModify == true || isset($_SESSION['superuser'])){
      $scanReq = isset($_GET['newComplaint']) || $newModify == true ? 'required' : '';

    $returnString = '<div style="margin-bottom: 5px; border: 2px solid black; width: 300px; padding-left: 5px;">'.
                    '<h4 style="margin-top: 0px;">Complaint Form Scan<h4><p><input id="formScanInput" type="file" name="formScanFile" accept="image/jpeg" '.$scanReq.'></input>'.
                    '</div>';
    }
    return $returnString;
  }
?>
