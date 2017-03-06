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

	var currentDate = new Date();
	var dateString = currentDate.getFullYear()+"-";
	
	if(currentDate.getMonth() < 10)
		dateString += "0";
		
	dateString += (currentDate.getMonth()+1)+"-";
	
	if(currentDate.getDate() < 10)
		dateString += "0";
		
	dateString += currentDate.getDate();
	
	$(".currentDate").html(dateString);

	if(kind == "pendingList"){
		heading = "#pendingListHeading";
		criteria = {"status": "pndg"};
	}
	else if(kind == "hearingListDaily"){
		heading = "#hearingListHeading";
		criteria = {"hearingDate": dateString};
	}

	$(heading).addClass("printHeading");
	getDBInfo(criteria).then(function(){
		if(kind == "pendingList")
			$(".pndgInvis").addClass("noPrint");
		window.print();
		getDBInfo(dbSearchCriteria);
		$(heading).removeClass("printHeading");
		$(heading).css("display","none");
		$(".pndgInvis").removeClass("noPrint");
	});
}

function makeFilter(key,value){
	if(dbSearchCriteria.hasOwnProperty(key))
		$(".filterDisplay:contains("+dressUpColumnName(key)+")").remove();

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
	
	var closeButton = $("<span class='filterCloseButton'>X</span>");
	closeButton.click(function(){
		if(key == "prefixAndCaseNumber"){
			delete dbSearchCriteria["prefix"];
			delete dbSearchCriteria["caseNumber"];
		}
		else
			delete dbSearchCriteria[key];
		
		getDBInfo(dbSearchCriteria);
		$(this).parent().remove();
	});
	
	var filter = $("<span class='filterDisplay noPrint'></span>");
	filter.html(dressUpColumnName(key)+": "+value);
	filter.append(closeButton);

	$("#currentFilters").append(filter);
}

var currentSort = {column: "prefixAndCaseNumber",
									 dir:		 "desc"};

function sortRows(column = null, dir = null){

	if(column == null){
		column = currentSort.column;
		dir = currentSort.dir;
	}
	else{
		currentSort.column = column;
		currentSort.dir = dir;
	}
	
	if(column == null){
		return;
	}
		
	var retVal;
	
	if(dir == "desc")
		retVal = 1;
	else if(dir == "asc")
		retVal = -1;
		
	if(column == "prefixAndCaseNumber"){
		rowObjects.sort(function(a,b){
		
		var aVal = parseInt(a.getCellValue("prefix")+a.getCellValue("caseNumber"));
		var bVal = parseInt(b.getCellValue("prefix")+b.getCellValue("caseNumber"));
		
		if(aVal < bVal)
			return retVal;
		else
			return -retVal;
	});
}
else if(column == "hearingDate"){
		rowObjects.sort(function(a,b){
			var aVal = parseInt(a.getCellValue(column).replace(/[^0-9]/g,""));
			var bVal = parseInt(b.getCellValue(column).replace(/[^0-9]/g,""));
			
			if(isNaN(aVal))
				aVal = 0;
			if(isNaN(bVal))
				bVal = 0;
			
			console.log("A AND B", aVal, bVal);
			
			if(aVal < bVal)
				return retVal;
			else
				return -retVal;
	});
}
else if(column == "plaintiff" || column == "witness"){
	rowObjects.sort(function(a,b){
		var aVal = a.getCellValue(column).toLowerCase().split(", ");
		aVal.sort();
		aVal = aVal.join("");
		
		var bVal = b.getCellValue(column).toLowerCase().split(", ");
		bVal.sort();
		bVal = bVal.join("");
		
		if(aVal < bVal)
			return retVal;
		else
			return -retVal;
	});
}
else{
	rowObjects.sort(function(a,b){
		if(a.getCellValue(column).toLowerCase() < b.getCellValue(column).toLowerCase())
			return retVal;
		else
			return -retVal;
	});
}
$(".arrow").remove();

if(dir == "asc"){
	$(mainTable.rows[0].cells[columnIndex[column]]).append(upArrow.clone());
}
else{
	$(mainTable.rows[0].cells[columnIndex[column]]).append(downArrow.clone());
}
}

function makeTable(dataSet){
	var returnArray = [];
	
	for(let i = 0; i < dataSet.length; i++){
			returnArray.push(new DatabaseRow(dataSet[i],rowObjects));
		}
		
	return returnArray;	
}

function fillTable(table){
	$("#updateDBButton").hide();
	while(table.rows.length > 0)
		table.deleteRow(-1);
		
		for(let i = 0; i < rowObjects.length; i++){
			$(table).append(rowObjects[i].returnRow());
		}
	return true;
}

function simpleHeadingMenuSetup(column){
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
