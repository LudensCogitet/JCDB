<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

  session_start();
  if(isset($_SESSION['superuser'])){
    if(isset($_POST['entryRowID']) && isset($_POST['statusRowID'])){
      try{
        $dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
                          ";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
                          $GLOBALS['_JCDB_config']['SQL_MODIFY_USER'],
                          $GLOBALS['_JCDB_config']['SQL_MODIFY_PASS'],
                          [PDO::ATTR_PERSISTENT => true]);

        $statement = $dbConn->prepare("DELETE FROM caseentries WHERE rowID = ?;");
        $statement->execute([$_POST['entryRowID']]);

        $statement = $dbConn->prepare("DELETE FROM casestatus WHERE rowID = ?;");
        $statement->execute([$_POST['statusRowID']]);
      }
      catch(Exception $e){
        print "Error!: ".$e->getMessage()."<br/>";
        return;
      }

      $dbConn = false;
      $statement = false;
      return 'done'.$_POST['entryRowID']." ".$_POST['statusRowID'];
    }
  }
?>
