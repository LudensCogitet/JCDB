<?php
  function checkLogout(){
    if(isset($_POST['LOGOUT'])){
      setcookie(session_name(),session_id(),1);
      session_unset();
      session_destroy();
    }
  }
?>
