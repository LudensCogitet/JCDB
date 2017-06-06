<!DOCTYPE html>
<html>
<head>
<?php
	require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
	session_start();
	require_once 'PHP/configIndexMenu.php';

	if(isset($_POST['LOGOUT'])){
		setcookie(session_name(),session_id(),1);
		session_unset();
		session_destroy();
	}
?>
<link rel="stylesheet" type="text/css" href="CSS/ComplaintForm.css">
<link rel="stylesheet" type="text/css" href="CSS/databaseDisplay.css">
<link rel="stylesheet" type="text/css" href="CSS/UI.css">
<script src="JS/jquery-3.1.1.min.js"></script>
<script src="JS/formatting.js"></script>
<script src="JS/ComplaintForm.js"></script>
<script src="JS/ContextMenu.js"></script>
<script src="JS/DatabaseRow.js"></script>
<script src="JS/tableFunctions.js"></script>
<script>
	var dbSearchCriteria = {};

	var dataSet = [];
	var rowObjects = {"array": [],
										"caseNumber": {}};
	var limits = {"offset": 0,
								"count": 30};

	var upArrow = $("<span class='arrow up noPrint'>&#x25B2;</span>");
	var downArrow = $("<span class='arrow down noPrint'>&#x25BC;</span>");

	function getDBInfo(criteria = "all", type = "overwrite", myLimits = limits){
		  DatabaseRow.autoFillDoNotAsk = [];
			var returnPromise = new Promise(function(resolve,reject){
			var check = false;
			if(typeof criteria == "object"){
				if(Object.keys(criteria).length == 0)
					criteria = "all"
			}

			//console.log(criteria);
			if(type == "overwrite"){
				limits['offset'] = 0;
			}

			$.ajax({url:"PHP/returnDBInfo.php",
				method: "GET",
				data:{"criteria": JSON.stringify(criteria),
							"limits": JSON.stringify(limits)},
				success: function(result){
					//console.log(result);
					result = JSON.parse(result);
					if(result[0] == true){
						$("#loadMore").show();
					}
					else{
						$("#loadMore").hide();
					}

					dataSet = result[1];

					if(type == "overwrite"){
						rowObjects = makeTable(dataSet);
					}
					else{
						rowObjects = makeTable(dataSet,rowObjects);
					}
					console.log(rowObjects);
					sortRows();
					fillTable(mainTable.tBodies[0],type);
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
		headingMenuSetup("notes");

		$("#updateDBButton").click(function(){
			if(confirm('Are you sure?')){
				for(let i = 0; i < rowObjects["array"].length; i++){
					//console.log("SENDING CHANGES");
					rowObjects["array"][i].sendChanges();
				}
				$(this).hide();
			}
		});

		getDBInfo();

		function windowClose(){
			$(this).parent().hide();
			$(this).siblings().children(":not(#updateComplaintButton)").remove();
			localStorage.removeItem('lastFormData');
		}

		$("#caseInfoClose").click(windowClose);

		$("html").click(function(){
			$("#contextMenu").hide();
			$(".contextMenuStyle").not("#contextMenu").remove();
			//console.log("HIDE TRIGGERED");
		});


		//$("#maintTable tbody").height($(window).height());
		//$(window).resize(function(){$("#mainTable tbody").height(0);$("#mainTable tbody").height($(window).height());});
		//$("#mainTable").height($(window).height());
  });
</script>
</head>
<body>
	<div id="pendingListHeading" style="display: none;">Judicial Committee<br>Hearing List<br><span class="useDate"></span></div>
	<div id="hearingListHeading" style="display: none;">Judicial Committee<br>JC Report<br><span class="useDate"></span></div>
	<div id="contextMenu" class="contextMenuStyle noPrint"></div>
	<div id="caseInfo" class="noPrint">
		<div id="caseInfoClose" class="UIButton fixedElClose">Close</div>
		<div id="caseTarget">
<?php
	if(isset($_SESSION['username'])){
?>
<span id="updateComplaintButton">
<form id="updateComplaintForm" name="updateComplaintForm" method='GET' target='_blank' action='enterCaseData.php'><input style='display: none;' type='submit' name='updateComplaint'></input></form>
			<div style='float:left' class="UIButton buttonLong" onclick="document.updateComplaintForm.updateComplaint.click();">Update Complaint</div>
</span>
<?php
	}
?>
		</div>
	</div>
<?php indexMenu(); ?>
<div id="currentFilters" class="filtersBox noPrint"></div>
<div id="tableContainer">
	<table id="mainTable">
		<thead id="mainTableHead">
			<tr>
				<th>Case No.</th><th>Plaintiff(s)</th><th>Defendant</th><th>Witness(es)</th><th>Charge</th><th class='pndgInvis'>Status</th><th class='pndgInvis'>Hearing Date</th><th class='pndgInvis'>Verdict</th><th class='pndgInvis'>Sentence</th><th class='pndgInvis'>Sentence Status</th><th class='pndgInvis'>Notes</th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
	<div class="UIButton buttonSmall noPrint" id="loadMore" onclick="limits['offset'] += limits['count']; getDBInfo(dbSearchCriteria,'add');">Load More...</div>
</div>
</body>
</html>
