<?php
function databaseConnect($type = 'read'){
  $dbConn = false;
  $SQLUser = 'SQL_VIEW_USER';
  $SQLPass = 'SQL_VIEW_PASS';

  if(isset($_SESSION['username']) && $type == 'write'){
    $SQLUser = 'SQL_MODIFY_USER';
    $SQLPass = 'SQL_MODIFY_PASS';
  }

  try{
    $dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
                      ";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
                      $GLOBALS['_JCDB_config'][$SQLUser],
                      $GLOBALS['_JCDB_config'][$SQLPass],
                      [PDO::ATTR_PERSISTENT => true]);
  }
  catch(Exception $e){
    echo "Error!:".$e->getMessage();
    $dbConn = false;
  }
  
  return $dbConn;
}
?>
