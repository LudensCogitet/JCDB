function ChargeTable(tableDiv){
  var mainTable = tableDiv;

  var dataSet = [];
  var rowObjects = {"array": [],
                    "caseNumber": {}};

  var numCellsChanged = 0

  var dbSearchCriteria = {};

  var currentSort = {column: "prefixAndCaseNumber",
                     dir:		 "desc"};

  var limits = {"offset": 0,
                "count": 30};

  var autoFillDoNotAsk = [];

  var upArrow = $("<span class='arrow up noPrint'>&#x25B2;</span>");
  var downArrow = $("<span class='arrow down noPrint'>&#x25BC;</span>");

  var columnIndex = {"prefixAndCaseNumber": 0,
                     "plaintiff": 1,
                     "defendant": 2,
                     "witness": 3,
                     "charge": 4,
                     "status": 5,
                     "hearingDate": 6,
                     "verdict": 7,
                     "sentence": 8,
                     "sentenceStatus": 9,
                     "notes": 10};

  getDBInfo();

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

  this.getDBInfo = getDBInfo;
  this.dressUpColumnName = dressUpColumnName;
  this.autoFillAsk = autoFillAsk;
  this.makeFilter = makeFilter;
  this.makeReport = makeReport;

  this.columnIndex = function(key){
      return columnIndex[key];
  }

  this.sendChanges = function(){
    for(let i = 0; i < rowObjects["array"].length; i++){
      rowObjects["array"][i].sendChanges();
    }
  }

  this.cellChanged = function(){
    var updateButton = $("#updateDBButton");
    numCellsChanged++;

    if(!updateButton.is(":visible")){
      updateButton.css('display','inline-block');
    }
  }

  this.cellChangedBack = function(){
    numCellsChanged--;
    if(numCellsChanged == 0)
      $("#updateDBButton").hide();
  }

  this.cellChangedReset = function(){
    numCellsChanged = 0;
  }

  this.loadMore = function(){
    limits['offset'] += limits['count'];
    getDBInfo(dbSearchCriteria,'add');
  }

  function getDBInfo(criteria = "all", type = "overwrite", myLimits = limits){
      autoFillDoNotAsk = [];
      return new Promise(function(resolve,reject){
      var check = false;
      if(typeof criteria == "object"){
        if(Object.keys(criteria).length == 0)
          criteria = "all"
      }

      if(type == "overwrite"){
        limits['offset'] = 0;
      }

      $.ajax({url:"PHP/index/returnDBInfo.php",
        method: "GET",
        data:{"criteria": JSON.stringify(criteria),
              "limits": JSON.stringify(limits)},
        success: function(result){
          if(result.search(/^error/i) != -1){
            console.error(result);
          }
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
          sortRows();
          fillTable(mainTable.tBodies[0],type);
          resolve("Yay");
      }});
    });
  }

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

  function autoFillAsk(key,row){
    var caseNumber = row.getCellValue("prefixAndCaseNumber").toString();
    var caseRows = rowObjects["caseNumber"][caseNumber];
    var value = row.getCellValue(key);

    var doAsk = true;
    if(key == 'sentence' || value == 'cmpl' || row.getCellValue('charge') == 'Exile' || row.getCellValue('charge') == 'Contempt'){
      doAsk = false;
    }
    else if(caseNumber in autoFillDoNotAsk){
      if(autoFillDoNotAsk[caseNumber][key] == undefined)
        doAsk = false;
    }

    if(doAsk && caseRows.length > 1){
      //console.log(key, "THIS IS THE KEY");
      var newMenu = $("<div class='contextMenuStyle'>");
      $("html").append(newMenu);

      var fillWith = ''; //key == 'sentenceStatus' && value == 'impsd' ? 'mrgd' : value;
      var fillWithText = '';
      var fillWithFunction = null;

      var numChargesForDefendant = caseRows.filter(function(el){
        return el.getCellValue('defendant') == row.getCellValue('defendant') &&
               el.getCellValue('charge') != 'Exile' &&
               el.getCellValue('charge') != 'Contempt';
      }).length;

      if(value == 'impsd' && numChargesForDefendant > 1){
        fillWith = 'mrgd';
        fillWithText = ' for this defendant?';
        fillWithFunction = function(neighbor){
                            if(neighbor.getCellValue('defendant') == row.getCellValue('defendant') &&
                               neighbor.getCellValue('charge') != 'Exile' &&
                               neighbor.getCellValue('charge') != 'Contempt'){
                              neighbor.setCellValue(key,fillWith);
                            }
                           }
      }
      else{
        fillWith = value;
        fillWithText = ' for this case?';
        fillWithFunction = function(neighbor){
                            if(neighbor.getCellValue('charge') != 'Exile' && neighbor.getCellValue('charge') != 'Contempt'){
                              neighbor.setCellValue(key,fillWith);
                            }
                           }
      }

      var options = [["Auto-fill "+dressUpColumnName(key)+" with \""+fillWith+"\""+fillWithText],
                    ["Yes",	function(cMenuDiv,clickable,optionVal){
                              for(let i = 0; i < caseRows.length; i++){
                                if(caseRows[i].getCellValue(key) != value)
                                  fillWithFunction(caseRows[i]);
                              }
                              $("#contextMenu").hide();
                              cMenuDiv.remove();
                            }],
                    ["No",function(cMenuDiv,clickable,optionVal){
                              if(!(caseNumber in autoFillDoNotAsk)){
                                //console.log("HIIIIII!")
                                autoFillDoNotAsk[caseNumber] = [];
                              }

                              autoFillDoNotAsk[caseNumber].push(key);
                              $("#contextMenu").hide();
                              cMenuDiv.remove();
                            }]];
      contextMenu(null,newMenu,options);
    }
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
  		getDBInfo(criteria,'overwrite').then(function(){
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
  		let dbRow = new DatabaseRow(dataSet[i],this);
  		let prefixAndCaseNumber = dbRow.getCellValue("prefixAndCaseNumber");

  		rowObjects["array"].push(dbRow);

  		if(Array.isArray(rowObjects["caseNumber"][prefixAndCaseNumber])){
  			rowObjects["caseNumber"][prefixAndCaseNumber].push(dbRow);
  		}
  		else{
  			rowObjects["caseNumber"][prefixAndCaseNumber] = [dbRow];
  		}
  	}
  	return rowObjects;
  }
  makeTable = makeTable.bind(this);

  function fillTable(table, type = "overwrite"){
		while(table.rows.length > 0)
			table.deleteRow(-1);

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
  		if(ChargeTable.multiChoiceFields[column] == undefined){
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
}

ChargeTable.multiChoiceFields = {"status":					["pndg","apld","hldg","clsd","(blank)"],
													 			 "verdict":					["ng", "g", "ni", "md", "wd", "(blank)"],
													 		 	 "sentenceStatus":	["impsd", "cmpl", "mrgd", "(blank)"]};

ChargeTable.textEntryFields 	=	{"hearingDate": true,
																 "sentence": true,
																 "notes": true};

ChargeTable.hearingFields = 		{"status": 					true,
																 "verdict": 				true,
																 "sentenceStatus":	true,
																 "hearingDate": 		true,
																 "sentence": 				true,
																 "notes":						true};
