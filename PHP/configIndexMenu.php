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
                    "<div style='top: -1px;' class='dropdownContentSubmenu'>".
                      "<div class='UIButton buttonShort' onclick='arguments[0].stopPropagation(); makeReport(\"pendingList\");'>Hearing List</div>".
                      "<div class='UIButton buttonShort' onclick='makeReport(\"hearingListDaily\");'>Daily JC Report</div>".
                      "<div class='UIButton buttonShort' onclick='window.print()'>Current Table</div>".
                    "</div>".
                  "</div>";

  $complaintOptions = "";
  if(isset($_SESSION['username'])){
   $complaintOptions = "<div class='UIButton buttonShort' onclick='window.open(\"enterCaseData.php?newComplaint=true\")'>Add New Complaint</div>";
  }

  $updateButton = "";
  if(isset($_SESSION['username'])){
   $updateButton = "<div style='position: fixed; right: 5px; z-index: 1001' class='UIButton buttonShort danger' id='updateDBButton'>Update Database</div>";
  }

  $menu = $menu.$loginOptions.$printOptions.$complaintOptions."</div></div>".$updateButton;

  echo $menu;
}
?>
