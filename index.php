<!DOCTYPE html>
<html>
	<head>
		<?php
			require_once $_SERVER['DOCUMENT_ROOT'].'/config.php';
			session_start();

			require_once 'PHP/index/menu.php';
			require_once 'PHP/index/updateCaseButton.php';
			require_once 'PHP/index/checkLogout.php';

			checkLogout();
		?>
		<link rel="stylesheet" type="text/css" href="CSS/ComplaintForm.css">
		<link rel="stylesheet" type="text/css" href="CSS/databaseDisplay.css">
		<link rel="stylesheet" type="text/css" href="CSS/UI.css">
	</head>
	<body>
		<div id="pendingListHeading" style="display: none;">Judicial Committee<br>Hearing List<br><span class="useDate"></span></div>
		<div id="hearingListHeading" style="display: none;">Judicial Committee<br>JC Report<br><span class="useDate"></span></div>
		<div id="contextMenu" class="contextMenuStyle noPrint"></div>
		<div id="caseInfo" class="noPrint">
			<div id="caseInfoClose" class="UIButton fixedElClose">Close</div>
			<div id="caseTarget">
			<?php updateCaseButton(); ?>
			</div>
		</div>
		<?php echo menu(); ?>
		<div id="currentFilters" class="filtersBox noPrint"></div>
		<div id="tableContainer">
			<table id="mainTable">
				<thead id="mainTableHead">
					<tr>
						<th>Case No.</th>
						<th>Plaintiff(s)</th>
						<th>Defendant</th>
						<th>Witness(es)</th>
						<th>Charge</th>
						<th class='pndgInvis'>Status</th>
						<th class='pndgInvis'>Hearing Date</th>
						<th class='pndgInvis'>Verdict</th>
						<th class='pndgInvis'>Sentence</th>
						<th class='pndgInvis'>Sentence Status</th>
						<th class='pndgInvis'>Notes</th>
					</tr>
				</thead>
				<tbody>
				</tbody>
			</table>
			<div class="UIButton buttonSmall noPrint" id="loadMore" onclick="limits['offset'] += limits['count']; getDBInfo(dbSearchCriteria,'add');">Load More...</div>
		</div>
		<script src="JS/jquery-3.1.1.min.js"></script>
		<script src="JS/formatting.js"></script>
		<script src="JS/complaintForm.js"></script>
		<script src="JS/ContextMenu.js"></script>
		<script src="JS/DatabaseRow.js"></script>
		<script src="JS/tableFunctions.js"></script>
		<script src="JS/index.js"></script>
	</body>
</html>
