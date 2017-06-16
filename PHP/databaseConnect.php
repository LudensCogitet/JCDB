<?php
function databaseConnect($type = 'read'){
  $dbConn = false;
  if(isset($_SESSION['username'])){
    $SQLUser = $type == 'write' ? 'SQL_MODIFY_USER' : 'SQL_VIEW_USER';
    $SQLPass = $type == 'write' ? 'SQL_MODIFY_PASS' : 'SQL_VIEW_PASS';

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
  }
  return $dbConn;
}
?>
