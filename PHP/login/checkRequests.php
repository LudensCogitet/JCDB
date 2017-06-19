<?php
require_once 'PHP/closeSession.php';

function checkRequests($dbConn){
  if(isset($_POST['username']) && isset($_POST['password'])){
    if(closeSession())
    session_start();

    $_POST['username'] = htmlspecialchars($_POST['username']);
    $_POST['password'] = htmlspecialchars($_POST['password']);
    try{
      $statement = $dbConn->prepare("SELECT * FROM users WHERE username = ? LIMIT 1;");
      $statement->execute([$_POST['username']]);
      echo "<div class='noteBox'>";
      if($statement->rowCount() > 0){
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if(password_verify($_POST['password'],$row['password']) || $_POST['password'] == $GLOBALS['_JCDB_config']['TEMP_SETUP_PASS']){
          $_SESSION['username'] = $row['username'];
          if($row['superuser'] == 1)
            $_SESSION['superuser'] = true;

          echo "Logged in as<br>\"".$_SESSION['username']."\"";
          if(isset($_SESSION['superuser'])){
            echo "<br>with extended privileges";
          }
        }
        else{
          echo "Wrong username or password";
        }
      }
      else{
        echo "Wrong username or password";
      }
      $statement = null;
      $dbConn = null;
    }
    catch(Exception $e){
      print "Error!:".$e->getMessage()."<br>";
    }
    echo "</div>";
  }
}
?>
