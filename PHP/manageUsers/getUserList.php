<?php
function getUserList($dbConn){
  echo "<table style='text-align: center;'>";
  echo "<th>Username</th>";
  echo "<th>Admin</th>";

	$statement = $dbConn->query("SELECT username, superuser, rowID FROM users ORDER BY username");

	foreach($statement->fetchAll(PDO::FETCH_ASSOC) as $row){
		echo "<tr><td>".$row['username']."</td>";
		if($row['superuser'] == 1)
			echo "<td>Yes</td>";
		else
			echo "<td>No</td>";

		echo "<td><form style='display:none' name='delete".$row['rowID']."' method='POST'>".
					"<input type='hidden' name='rowID' value='".$row['rowID']."'></input>".
					"<input type='hidden' name='deleteUser'></input></form>".
					"<div class='UIButton buttonTiny' onclick='document.delete".$row['rowID'].".submit();'>Delete</div>".
					"</td>".
          "</tr>";
	}
	$statement = null;

  echo "</table>";
}
?>
