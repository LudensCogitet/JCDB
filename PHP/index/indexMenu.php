<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';

function indexMenu(){
  $menu = "<div class='menu noPrint'>";

  $superUserOptions = "";
  if(isset($_SESSION['superuser'])){
   $superUserOptions = "<div class='UIButton buttonShort' onclick='location.href=\"manageUsers.php\"'>Manage Users</div>";
  }

  if(isset($_SESSION['username'])){
   $loginOptions =  "<div class='dropdown sideBySide moveRight'>".
                    "<div class='UIButton buttonShort'>Logged in</div>".
                    "<div class='dropdownContent'>".
                    "<div class='noteBox'>".$_SESSION['username']."</div>".
                    "<form method='POST' name='logoutButton'>".
   		               "<input type='hidden' name='LOGOUT'></input>".
   		              "</form>".
                    $superUserOptions.
   		              "<div class='UIButton buttonShort' onclick='document.logoutButton.submit();'>Log out</div></div></div>";
  }
  else{
    $loginOptions = "<div class='UIButton buttonShort sideBySide moveRight' onclick='location.href=\"login.php\"'>Log In</div>";
  }

  $printOptions = "<div class='dropdown sideBySide moveRight'>".
                    "<div class='UIButton buttonShort'>Print</div>".
                    "<div class='dropdownContent'>".
                      "<div class='UIButton buttonShort' onclick='arguments[0].stopPropagation(); makeReport(\"pendingList\");'>Hearing List</div>".
                      "<div class='UIButton buttonShort' onclick='makeReport(\"hearingListDaily\");'>Daily JC Report</div>".
                      "<div class='UIButton buttonShort' onclick='window.print()'>Current Table</div>".
                    "</div>".
                  "</div>";

  $complaintOptions = "";
  if(isset($_SESSION['username'])){
   $complaintOptions = "<div class='UIButton buttonShort sideBySide moveRight' onclick='window.open(\"enterCaseData.php?newComplaint=true\")'>New Case</div>";
  }

  $updateButton = "";
  if(isset($_SESSION['username'])){
   $updateButton = "<div class='UIButton buttonShort danger sideBySide moveRight' id='updateDBButton'>Update Database</div>";
  }

  $menu = $menu.$loginOptions.$printOptions.$complaintOptions.$updateButton."</div>";

  echo $menu;
}
?>
