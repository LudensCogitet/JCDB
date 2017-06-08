<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
require_once 'PHP/CaseData.php';
require_once 'PHP/displayCase.php';
session_start();

function convertToText($key, $value){
  $newKey = preg_replace('/(?<!\ )[A-Z]/', ' $0', $key);
  $newKey = strtoupper($newKey);
  $newVal = preg_replace('/\<br \/\>/', '\n', $value);
  return $newKey."\n".$newVal."\n\n";
}

if(isset($_SESSION['username'])){
  if(isset($_POST['prefix']) && isset($_POST['caseNumber'])){
    $prefix = $_POST['prefix'];
    $caseNumber = $_POST['caseNumber'];

    $complaint = grabCase($prefix,$caseNumber)[0];
    $caseNotes = grabCaseNotes($prefix,$caseNumber);

    $content = "";
    $fullCaseNum = "";
    foreach($complaint as $key => $value){
      if($key == 'rowID' || $key == 'formScan')
        continue;
      else{
        if($key == 'prefix'){
          $fullCaseNum = "CASE NUMBER\n".$value."-";
        }
        else if($key == 'caseNumber'){
          $fullCaseNum = $fullCaseNum.$value."\n\n";
          $content = $content.$fullCaseNum;
        }
        else{
          $content = $content.convertToText($key,$value);
        }
      }
    }

    $content = $content."----\n";

    foreach($caseNotes as $note){

      $content = $content.$note['timeEntered']."\n".convertToText('note',$note['note']).convertToText('author',$note['author']);
    }
    echo "<textarea style='width: 100%; height: 100%;'>".$content."</textarea>";
  }
}
?>
