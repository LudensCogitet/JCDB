<?php
  function checkUserPermissions(...$permissions){
    foreach($permissions as $permission){
      if(!isset($_SESSION[$permission])){
        echo "<h2>Oops.. You're not allowed to see this page!</h2>";
        exit;
      }
    }
  }
?>
