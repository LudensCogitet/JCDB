<html>
<head>
<?php
	session_start();
	if(isset($_POST['LOGOUT'])){
		setcookie(session_name(),session_id(),1);
		session_unset();
		session_destroy();
	}
?>
<link rel="stylesheet" type="text/css" href="./CSS/ComplaintForm.css">
<link rel="stylesheet" type="text/css" href="./CSS/databaseDisplay.css">
<script src="./JS/jquery-3.1.1.min.js"></script>
<script src="./JS/ComplaintForm.js"></script>
<script src="./JS/ContextMenu.js"></script>
<script src="./JS/DatabaseRow.js"></script>
<script src="./JS/tableFunctions.js"></script>
<script>
  localStorage.removeItem("lastFormData"); 
	
	var dbSearchCriteria = {};
	
	var dataSet = [];
	var rowObjects = [];

	var upArrow = $("<span class='arrow up noPrint'>&#x25B2;</span>");
	var downArrow = $("<span class='arrow down noPrint'>&#x25BC;</span>");

	function getDBInfo(criteria = "all"){
			var returnPromise = new Promise(function(resolve,reject){
			var check = false;
			if(typeof criteria == "object"){
				if(Object.keys(criteria).length == 0)
					criteria = "all"
			}
			
			console.log(criteria);
			
			$.ajax({url:"./PHP/returnDBInfo.php",
				method: "GET",
				data:{"criteria": JSON.stringify(criteria)}, 
				success: function(result){
					console.log(result);
					dataSet = JSON.parse(result);
					rowObjects = makeTable(dataSet);
					sortRows();
					fillTable(mainTable.tBodies[0]);
					resolve("Yay");
			}});
		});

		return returnPromise;
	}

	var mainTable;

  $(document).ready(function(){
		mainTable = document.getElementById("mainTable");
		
		headingMenuSetup("prefixAndCaseNumber");
		headingMenuSetup("plaintiff");
		headingMenuSetup("defendant");
		headingMenuSetup("witness");
		headingMenuSetup("charge");
		headingMenuSetup("status");
		headingMenuSetup("hearingDate");
		headingMenuSetup("verdict");
		headingMenuSetup("sentence");
		headingMenuSetup("sentenceStatus");
		
		$("#updateDBButton").click(function(){	
			if(confirm('Are you sure?')){
				for(let i = 0; i < rowObjects.length; i++){
					console.log("SENDING CHANGES");
					rowObjects[i].sendChanges();
				}
			}
		});
		
		getDBInfo();
  
		function windowClose(){
			$(this).parent().hide();
			$(this).siblings().children().remove();
			localStorage.removeItem('lastFormData');
		}
		
		$("#caseInfoClose").click(windowClose);
		
		$("html").click(function(){
			$("#contextMenu").hide();
			$(".contextMenuStyle").not("#contextMenu").remove();
			console.log("HIDE TRIGGERED");
		});
		
		
		//$("#maintTable tbody").height($(window).height());
		//$(window).resize(function(){$("#mainTable tbody").height(0);$("#mainTable tbody").height($(window).height());});
		//$("#mainTable").height($(window).height());
  });
</script>
</head>
<body>
	<div id="pendingListHeading" style="display: none;">Judicial Committee<br>Hearing List<br><span class="currentDate"></span></div>
	<div id="hearingListHeading" style="display: none;">Judicial Committee<br>JC Report<br><span class="currentDate"></span></div>
	<div id="contextMenu" class="contextMenuStyle noPrint"></div>
	<div id="caseInfo" class="noPrint">
		<button id="caseInfoClose" class="fixedElClose">Close</button>
		<div id="caseTarget"></div>
	</div>
	<div id="currentFilters" class="noPrint"></div>
<span class='noPrint'>
<?php 
	if(!isset($_SESSION['username'])){?>
		<a href='./PHP/login.php'>login</a>
<?php 
	}
	else{	
		echo "Currently logged in as ".$_SESSION['username'];
?>
		<form method="POST">
			<input type="submit" name="LOGOUT" value="Logout"></input>
		</form>
<?php
		if(isset($_SESSION['superuser']))
		{
?>
			<a href='./PHP/newUser.php'>Add new account</a>

<?php
		}
	}
?>
</span>
	
	<button style="float:right; clear: both;" onclick="makeReport('pendingList');" class="noPrint">Print Hearing List</button>
	<button style="float:right; clear: both;" onclick="makeReport('hearingListDaily');" class="noPrint">Print Daily JC Report</button>
	<button style="float:right; clear: both;" onclick='window.print()' class="noPrint">Print</button>
	<?php if(isset($_SESSION['username'])){ ?>
		<button style="float:right; clear: both;" onclick='window.location.href="./PHP/enterComplaintData.php"' class="noPrint">Add New Complaint</button>
		<button id="updateDBButton" style="float:right;clear:left;" class="noPrint">Update Database</button>
	<?php } ?>
	<table id="mainTable" style="margin: auto; clear:both;">
		<thead id="mainTableHead">
			<tr>
				<th>Case No.</th><th>Plaintiff(s)</th><th>Defendant</th><th>Witness(es)</th><th>Charge</th><th class='pndgInvis'>Status</th><th class='pndgInvis'>Hearing Date</th><th class='pndgInvis'>Verdict</th><th class='pndgInvis'>Sentence</th><th class='pndgInvis'>Sentence Status</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</body>
</html>