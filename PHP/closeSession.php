<?php
function closeSession(){
  if(session_status() == PHP_SESSION_ACTIVE){
    if(isset($_SESSION['username'])){
      setcookie(session_name(),session_id(),1);
      session_unset();
      session_destroy();

      unset($_SESSION['username']);
      if(isset($_SESSION['superuser'])){
        unset($_SESSION['superuser']);
      }
      return true;
    }
  }
  return false;
}
?>
