<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';


function indexMenu(){
  $menu = "<div class='dropdown noPrint'>".
            "<div class='UIButton buttonShort'>Menu</div>".
            "<div class='dropdownContent'>";

  $superUserOptions = "";
  if(isset($_SESSION['superuser'])){
   $superUserOptions = "<div class='UIButton buttonShort' onclick='location.href=\"manageUsers.php\"'>Manage Users</div>";
  }

  $loginOptions = "<div class='UIButton buttonShort' onclick='location.href=\"login.php\"'>Log In</div>";
  if(isset($_SESSION['username'])){
   $loginOptions = "<div class='noteBox'>Logged in as ".$_SESSION['username']."</div>".
   		                 "<form method='POST' name='logoutButton'>".
   		                  "<input type='hidden' name='LOGOUT'></input>".
   		                 "</form>".
                     $superUserOptions.
   		               "<div class='UIButton buttonShort' onclick='document.logoutButton.submit();'>Log out</div>";
  }

  $printOptions = "<div class='dropdownSubmenu'>".
                    "<div class='UIButton buttonShort'>Print</div>".
                    "<div class='dropdownContentSubmenu'>".
                      "<div class='UIButton buttonShort' onclick='arguments[0].stopPropagation(); makeReport(\"pendingList\");'>Print Hearing List</div>".
                      "<div class='UIButton buttonShort' onclick='makeReport(\"hearingListDaily\");'>Print Daily JC Report</div>".
                      "<div class='UIButton buttonShort' onclick='window.print()'>Print</div>".
                    "</div>".
                  "</div>";

  $complaintOptions = "";
  if(isset($_SESSION['username'])){
   $complaintOptions = "<div class='UIButton buttonShort' onclick='window.location.href=\"enterCaseData.php?newComplaint=true\"'>Add New Complaint</div>";
  }

  $databaseOptions = "";
  if(isset($_SESSION['username'])){
   $databaseOptions = "<div class='UIButton buttonShort danger' id='updateDBButton'>Update Database</div>";
  }

  $menu = $menu.$loginOptions.$printOptions.$complaintOptions.$databaseOptions."</div></div>";

  echo $menu;
}
?>
