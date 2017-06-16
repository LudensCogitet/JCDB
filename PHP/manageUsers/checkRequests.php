<?php
  function checkRequests($dbConn){
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['superuser'])){
      if(isset($_POST['deleteUser'])){
        echo "<div class='noteBox'>";
        $statement = $dbConn->query("SELECT rowID FROM users WHERE superuser = 1");
        $numSupers = $statement->rowCount();
        $statement = $dbConn->prepare("SELECT username, superuser FROM users WHERE rowID = ?");
        $statement->execute([$_POST['rowID']]);
        $targetRow = $statement->fetch(PDO::FETCH_ASSOC);

        if($numSupers == 1 && $targetRow['superuser'] == 1){
          echo "Cannot delete last admin user";
        }
        else if($targetRow['username'] == $_SESSION['username']){
          echo "Cannot delete yourself!";
        }
        else{
          echo "User \"".$targetRow['username']."\" deleted";
          $statement = $dbConn->prepare("DELETE FROM users WHERE rowID = ?");
          $statement->execute([$_POST['rowID']]);
        }
        echo "</div>";
      }
      else if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['passwordConfirm'])){
        echo "<div class='noteBox'>";
        $_POST['username'] = htmlspecialchars($_POST['username']);
        $_POST['password'] = htmlspecialchars($_POST['password']);
        $_POST['passwordConfirm'] = htmlspecialchars($_POST['passwordConfirm']);

        if(isset($_SESSION['superuser'])){
          if($_POST['password'] != $_POST['passwordConfirm']){
            echo "Passwords do not match.";
          }
          else{
              $pHash = password_hash($_POST['password'],PASSWORD_DEFAULT);

              $isSuperuser = 0;
              if(isset($_POST['isSuperuser']))
                $isSuperuser = 1;

              $statement = $dbConn->prepare("INSERT INTO users(username,password,superuser) VALUES (?,?,?)");

              if(!$statement->execute([$_POST['username'],$pHash,$isSuperuser])){
                if($statement->errorCode() == 23000){
                  echo "Account already exists";
                }
                else{
                  echo "Unable to perform action (".$statement->errorCode()."). Please contact system admin.";
                }
              }
              else
                echo "Account \"".$_POST["username"]."\" added.";

              $statement = null;
            echo "</div>";
          }
        }
      }
    }
  }
?>
