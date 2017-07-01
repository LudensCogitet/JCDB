<?php
  require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
  require_once 'PHP/caseDataFunctions.php';

  if(isset($_POST['prefix']) && isset($_POST['caseNumber']) && isset($_POST['type'])){
    $formScanButton = isset($_POST['formScanButton']) ? true : false;
    echo caseForm('existing',$formScanButton,$_POST['prefix'], $_POST['caseNumber']);
  }
  else if(isset($_POST['type'])){
    if($_POST['type'] === 'new')
      echo caseForm();
  }

  // possible types: 'new', 'cached', 'existing'
  function caseForm($type = 'new', $formScanButton = true, $prefix = '', $caseNumber = ''){
    $data = null;

    if(session_status() != PHP_SESSION_ACTIVE){
      session_start();
    }
    $readOnly = $type === 'new' || isset($_SESSION['superuser']) ? '' : 'readonly';

    switch($type){
      case 'cached':
      case 'cachedWithCaseNote':
        if(isset($_SESSION['complaint']))
          $data = $_SESSION['complaint']->getData();
      break;
      case 'existing':
        if($prefix !== '' && $caseNumber !== '')
          $data = grabCase($prefix,$caseNumber);
      break;
    }

    $printPrefix = '';
    $printCaseNumber = '';
    $returnString = '<table class="complaintTable" id="mainComplaint">';

    $whatHappened = '';
    if($type == 'existing' || $type == 'cached' || $type == 'cachedWithCaseNote'){
        $whatHappened = $data["whatHappened"];
        $returnString .= '<input type="hidden" value="'.$data["formScan"].'" name="formScan"></input>';
        if($data['prefix'] !== -1 && $data['caseNumber'] !== -1){
          $printPrefix = $data["prefix"];
          $printCaseNumber = $data["caseNumber"];

          $returnString .= '<input type="hidden" value="'.$data["prefix"].'" name="prefix"></input>';
          $returnString .= '<input type="hidden" value="'.$data["caseNumber"].'" name="caseNumber"></input>';
        }
    }

    $formScanButton = $type === 'existing' && $formScanButton === true ?
    '<div class="UIButton buttonMedium" onclick="window.open(\'scanDisplay.php?scanSrc='.$data['formScan'].'\',\'_blank\');">View Complaint Scan</div>' : '';

    $caseNoTD = '<td class="textField" id="caseNumber">' . $printPrefix . '-' . $printCaseNumber . '</td>' .
                    '<td colspan=2 style="text-align: center" id="caseScanTarget">'.
                      $formScanButton.
                    '</td>';

    $returnString .= '<tr>'.
      '<th>Case No.</th>'.
      $caseNoTD.
      '</tr>' .
      '<tr>' .
      '<th>Plaintiff</th>' .
      '<td class="textField" id="plaintiff">' . makeReproducableInputFields("plaintiff",$readOnly,$data) . '</td>' .
      '<th>Date of Incident (YYYY-MM-DD)</th>' .
      '<td class="textField" id="dateOfIncident">' . makeReproducableInputFields("dateOfIncident",$readOnly,$data) . '</td>' .
      '</tr>' .
      '<tr>' .
      '<th>Defendant</th>' .
      '<td class="textField" id="defendant">' . makeReproducableInputFields("defendant",$readOnly,$data) . '</td>' .
      '<th>Time of Incident</th>' .
      '<td class="textField" id="timeOfIncident">' . makeReproducableInputFields("timeOfIncident",$readOnly,$data) . '</td>' .
      '</tr>' .
      '<tr>' .
      '<th>Witness</th>' .
      '<td class="textField" id="witness">' . makeReproducableInputFields("witness",$readOnly,$data) . '</td>' .
      '<th>Location</th>' .
      '<td class="textField" id="location">' . makeReproducableInputFields("location",$readOnly,$data) . '</td>' .
      '</tr>' .
      '<tr>' .
      '<th>What happened</th>' .
      '<td class="areaField" id="whatHappened"><textarea name="whatHappened" required '.$readOnly.'>' . $whatHappened . '</textarea></td>' .
      '<th>Charge & Sec. Number</th>' .
      '<td class="textField" id="charge">' . makeReproducableInputFields("charge",$readOnly,$data) . '</td>' .
      '</tr>' .
      '</table>'.
      '<script>'.
        '$("#mainComplaint").find("input").keydown(reproduceField);'.
        '$("#mainComplaint").find("input").blur(formatCheck);'.
      '</script>';

    if($type === 'cachedWithCaseNote'){
      $caseNoteText = '';
      if($data['caseNote'] !== false){
        $caseNoteText = str_replace("<br />", "",$data['caseNote']['note']);
      }
      return [$returnString,$caseNoteText];
    }

    return $returnString;
  }

  function makeReproducableInputFields($type, $readOnly, $data = null) {
		$coda = '" required '.$readOnly.'></input>';

		if($type === "witness")
			$coda = '" '.$readOnly.'></input>';

		$emptyInput = '<input type="text" name="' . $type . '[]" data-repro="false"'.$coda;

    if($data !== null){
      $entries = explode(', ',$data[$type]);
			$repro = "true";
			$returnString = "";
			for($i = 0; $i < count($entries); $i++) {
				if ($i === count($entries) - 1)
					$repro = "false";

				$returnString .= '<input type="text" name="' . $type . '[]" data-repro="' . $repro . '" value="' . $entries[$i] . $coda;
			}
			return $returnString;
		}
		else{
			return $emptyInput;
    }
  }
?>
