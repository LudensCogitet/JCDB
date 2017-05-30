<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

  session_start();
  if(isset($_SESSION['superuser'])){
    if(isset($_POST['rowID'])){
      try{
        $dbConn = new PDO("mysql:host=".$GLOBALS['_JCDB_config']['SQL_HOST'].
                          ";dbname=".$GLOBALS['_JCDB_config']['SQL_DB'],
                          $GLOBALS['_JCDB_config']['SQL_MODIFY_USER'],
                          $GLOBALS['_JCDB_config']['SQL_MODIFY_PASS'],
                          [PDO::ATTR_PERSISTENT => true]);

        $statement = $dbConn->prepare("DELETE FROM casenotes WHERE rowID = ?;");
        $statement->execute([$_POST['rowID']]);
      }
      catch(Exception $e){
        print "Error!: ".$e->getMessage()."<br/>";
        return;
      }

      $dbConn = false;
      $statement = false;
      return 'done';
    }
  }
?>
