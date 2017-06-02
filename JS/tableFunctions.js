function dressUpColumnName(name){
	var returnString;
	if(name == "prefixAndCaseNumber"){
		returnString = "Case No.";
	}
	else{
		returnString = name.charAt(0).toUpperCase() + name.slice(1);;

		var breakPoint = name.search(/[A-Z]/);

		if(breakPoint != -1){
			returnString = returnString.slice(0,breakPoint) + " " + returnString.slice(breakPoint);
		}
	}
	return returnString;
}

function makeReport(kind){
	var heading;
	var criteria;

	var useDate = new Date();

	if(kind == "pendingList"){
		var newMenu = $("<div class='contextMenuStyle'>");
		var inputDiv = $("<div id='pendingListInputDiv' style='text-align: center; margin-bottom: 2px; font-size: 20px'>"+
											"<input id='pendingYear' onclick='arguments[0].stopPropagation()' style='width: 2.7em' type='text' placeholder='YYYY'>"+
											"<input id='pendingMonth' onclick='arguments[0].stopPropagation()' style='width: 1.8em' type='text' placeholder='MM'>"+
											"<input id='pendingDay' onclick='arguments[0].stopPropagation()' style='width: 1.6em' type='text' placeholder='DD'>"+
										 "</div>");
		$("html").append(newMenu);
		var options = [["Enter Date", function(cMenuDiv,clickable,optionaVal){
										var year = cMenuDiv.find("#pendingYear").val();
										var month = cMenuDiv.find("#pendingMonth").val();
										var day = cMenuDiv.find("#pendingDay").val();

										var newDate = new Date(parseInt(year),parseInt(month)-1,parseInt(day));

										if(isNaN(newDate.getTime())){
												 alert("Please enter a valid year, month, and day.");
										}
									  else{
											useDate.setTime(newDate);
											cMenuDiv.remove();
											goToPrint();
										}
									}],
									["Use Next Weekday",function(cMenuDiv,clickable,optionaVal){
											switch(useDate.getDay()){
												case 5:
													useDate.setDate(useDate.getDate() + 3);
												break;
												case 6:
													useDate.setDate(useDate.getDate() + 2);
												break;
												default:
													useDate.setDate(useDate.getDate() + 1);
											}
											cMenuDiv.remove()
											goToPrint();
										}]];
		contextMenu(null,newMenu,options,'center');
		newMenu.prepend(inputDiv);
	}
	else{
		goToPrint();
	}

	function goToPrint(){
		var dateString = useDate.getFullYear()+"-";

		if(useDate.getMonth() < 10)
			dateString += "0";

		dateString += (useDate.getMonth()+1)+"-";

		if(useDate.getDate() < 10)
			dateString += "0";

		dateString += useDate.getDate();

		$(".useDate").html(dateString);

		if(kind == "pendingList"){
			heading = "#pendingListHeading";
			criteria = {"status": "pndg"};
		}
		else if(kind == "hearingListDaily"){
			heading = "#hearingListHeading";
			criteria = {"hearingDate": dateString};
		}

		$(heading).addClass("printHeading");
		getDBInfo(criteria,'overwrite',{'offset':0,'count':500}).then(function(){
			if(kind == "pendingList")
				$(".pndgInvis").addClass("noPrint");
			window.print();
			getDBInfo(dbSearchCriteria);
			$(heading).removeClass("printHeading");
			$(heading).css("display","none");
			$(".pndgInvis").removeClass("noPrint");
		});
	}
}

function makeFilter(key,value){
	$("#currentFilters").children("#"+key+"Filter").children(".filterCloseButton").click();

	if(key == "prefixAndCaseNumber"){
		if(value.length == 4){
			dbSearchCriteria["prefix"] = value;
		}
		else if(value.length > 5 && value.indexOf("-") != -1){
			dbSearchCriteria["prefix"] = value.slice(0,value.indexOf("-"));
			dbSearchCriteria["caseNumber"] = value.slice(value.indexOf("-")+1);
		}
	}
	else{
		dbSearchCriteria[key] = value;
	}

	var closeButton = $("<span class='filterCloseButton'>&#10006;</span>");
	closeButton.click(function(){
		if(key == "prefixAndCaseNumber"){
			delete dbSearchCriteria["prefix"];
			if(value.length > 5)
				delete dbSearchCriteria["caseNumber"];
		}
		else
			delete dbSearchCriteria[key];

		getDBInfo(dbSearchCriteria);
		$(this).parent().remove();
	});

	var filter = $("<span class='filterDisplay noPrint' id ='"+key+"Filter'></span>");
	if(value == "")
		filter.html(dressUpColumnName(key)+": (blank)");
	else
		filter.html(dressUpColumnName(key)+": "+value);
	filter.append(closeButton);

	$("#currentFilters").append(filter);
}

var currentSort = {column: "prefixAndCaseNumber",
									 dir:		 "desc"};

function sortRows(column = null, dir = null, column2 = "default"){

	function sortBy(column,retVal,a,b){
		var aVal;
		var bVal;

		if(column == "prefixAndCaseNumber"){
			aVal = parseInt(a.getCellValue("prefix")+a.getCellValue("caseNumber"));
			bVal = parseInt(b.getCellValue("prefix")+b.getCellValue("caseNumber"));
		}
		else if(column == "hearingDate"){
			aVal = parseInt(a.getCellValue(column).replace(/[^0-9]/g,""));
			bVal = parseInt(b.getCellValue(column).replace(/[^0-9]/g,""));

			if(isNaN(aVal))
				aVal = 0;
			if(isNaN(bVal))
				bVal = 0;
		}
		else if(column == "plaintiff" || column == "witness"){
			aVal = a.getCellValue(column).toLowerCase().split(", ");
			aVal.sort();
			aVal = aVal.join("");

			bVal = b.getCellValue(column).toLowerCase().split(", ");
			bVal.sort();
			bVal = bVal.join("");
		}
		else{
			aVal = a.getCellValue(column).toLowerCase();
			bVal = b.getCellValue(column).toLowerCase();
		}

		if(aVal < bVal)
			return retVal;
		else if(aVal > bVal)
			return -retVal;
		else
			return 0;
	}

	if(column == null){
		column = currentSort.column;
		dir = currentSort.dir;
	}
	else{
		currentSort.column = column;
		currentSort.dir = dir;
	}

	if(column2 == "default"){
		if(column == "prefixAndCaseNumber")
			column2 = "defendant";
		else
			column2 = "prefixAndCaseNumber";
	}

	if(column == null){
		return;
	}

	var retVal;

	if(dir == "desc")
		retVal = 1;
	else if(dir == "asc")
		retVal = -1;

	var retVal2 = -1;
	if(column2 == "prefixAndCaseNumber")
		retVal2 = 1;

	rowObjects["array"].sort(function(a,b){
		var returnVal = sortBy(column,retVal,a,b);
		if(returnVal == 0){
			returnVal = sortBy(column2,retVal2,a,b);
		}
		return returnVal;
	});

	$(".arrow").remove();

	if(dir == "asc"){
		$(mainTable.rows[0].cells[columnIndex[column]]).append(upArrow.clone());
	}
	else{
		$(mainTable.rows[0].cells[columnIndex[column]]).append(downArrow.clone());
	}
}

function makeTable(dataSet,rowObjects = null){
	if(rowObjects == null){
		rowObjects = {"array": [],
									"caseNumber": {}};
	}

	for(let i = 0; i < dataSet.length; i++){
		let dbRow = new DatabaseRow(dataSet[i],rowObjects);
		let prefixAndCaseNumber = dbRow.getCellValue("prefixAndCaseNumber");

		rowObjects["array"].push(dbRow);

		if(Array.isArray(rowObjects["caseNumber"][prefixAndCaseNumber])){
			rowObjects["caseNumber"][prefixAndCaseNumber].push(dbRow);
		}
		else{
			rowObjects["caseNumber"][prefixAndCaseNumber] = [dbRow];
		}
	}
	console.dir(rowObjects);
	return rowObjects;
}

function fillTable(table, type = "overwrite"){
	//if(type == "overwrite"){
		while(table.rows.length > 0)
			table.deleteRow(-1);
	//}

	var lastValue = null;

	for(let i = 0; i < rowObjects["array"].length; i++){
		if(i != 0 &&
			lastValue != rowObjects["array"][i].getCellValue(currentSort.column)){
				$(table).append($("<tr style='border: none; height: 10px' />"));
			}
			lastValue = rowObjects["array"][i].getCellValue(currentSort.column);

		$(table).append(rowObjects["array"][i].returnRow());
	}
	return true;
}

function headingMenuSetup(column){
		var searchFunc = null;
		if(multiChoiceFields[column] == undefined){
			searchFunc = function(cMenuDiv,clickable,optionVal){
			toggleTextField(cMenuDiv.children(":contains('Search')"),"input",
			function(value){
				cMenuDiv.hide();
				makeFilter(column,value);
				getDBInfo(dbSearchCriteria);
			});
		}
	}
	else{
		searchFunc = function(cMenuDiv,clickable,optionVal){
			var newMenu = $("<div class='contextMenuStyle'>");
			$("html").append(newMenu);

			var options = [];
			fillMultiChoiceMenu(options,column,function(cMenuDiv,clickable,optionVal){
				makeFilter(column,optionVal);
				getDBInfo(dbSearchCriteria);
				$("#contextMenu").hide();
				cMenuDiv.remove();
			});

			contextMenu(null,newMenu,options);
		}
	}

 var options = [["Search", searchFunc],
								["Sort By"],["asc",function(cMenuDiv){
																					cMenuDiv.hide();
																					//$(".arrow").remove();
																					sortRows(column,"asc");
																					fillTable(mainTable.tBodies[0]);
																					//$(mainTable.rows[0].cells[columnIndex[column]]).append(upArrow.clone());
																				}],
															 ["desc",function(cMenuDiv){
																					cMenuDiv.hide();
																					//$(".arrow").remove();
																					sortRows(column,"desc");
																					fillTable(mainTable.tBodies[0]);
																					//$(mainTable.rows[0].cells[columnIndex[column]]).append(downArrow.clone());
																				}]];

		contextMenu(mainTable.rows[0].cells[columnIndex[column]],"#contextMenu",options);
	}

var columnIndex = {"prefixAndCaseNumber":0,"plaintiff":1,"defendant":2,"witness":3,"charge":4,"status":5,"hearingDate":6,"verdict":7,"sentence":8,"sentenceStatus":9,"notes":10};
