var _highlightKeyDown = false;
$(window).keydown(function(event){if(event.which == 72)_highlightKeyDown = true;});
$(window).keyup(function(event){if(event.which == 72)_highlightKeyDown = false;});

function DatabaseRow(rawData,myTable){
	var entriesChanged = {"plaintiff":			false,
												"defendant":			false,
												"witness":				false,
												"charge":					false,
												"status": 				false,
												"verdict": 				false,
												"sentenceStatus":	false,
												"hearingDate": 		false,
												"sentence": 			false,
												"notes":					false};

	var initialData = {
		"prefix": 				rawData[0],
		"caseNumber":			rawData[1],
		"plaintiff":			rawData[2],
		"defendant":			rawData[3],
		"witness":				rawData[4],
		"charge":					rawData[5],
		"status":					rawData[6],
		"hearingDate":		rawData[7],
		"verdict":				rawData[8],
		"sentence":				rawData[9],
		"sentenceStatus":	rawData[10],
		"notes":					rawData[11],
		"rowID":					rawData[12]};

	Object.keys(initialData).forEach(function(key){
		if(initialData[key] === null){
			initialData[key] = "";
		}
	});

	var data = JSON.parse(JSON.stringify(initialData));

	var myRow = $("<tr>");
	var myCells = [$("<td>")];
	var myself = this;

	fillCells();

	for(let i = 0; i < myCells.length; i++)
		myRow.append(myCells[i]);

	function fillCells(){
		myCells[0].append(data["prefix"]+"-"+data["caseNumber"]);
		myCells[0].click(function(){
			complaintForm("#caseTarget",[data['prefix'],data['caseNumber']]);
			$("#updateCaseForm").append("<input type='hidden' name='prefix' value='"+data['prefix']+"'>");
			$("#updateCaseForm").append("<input type='hidden' name='caseNumber' value='"+data['caseNumber']+"'>");
			$("#caseInfo").show();
		});

		addHighlightOptions(myCells[0],"prefixAndCaseNumber");

		Object.keys(data).forEach(function(key){
			if(key === "prefix" || key == "caseNumber" || key == "rowID"){
				return;
			}
			else
			{
				myCells.push($("<td>"));
				var currentCell = myCells[myCells.length-1];
				if(ChargeTable.hearingFields[key]){
					//console.log("ADDING PDNGINVIS");
					currentCell.addClass("pndgInvis");
					currentCell.addClass(key);
				}

				addHighlightOptions(currentCell,key);

				currentCell.append(data[key]);

				assignClick(currentCell, key);
			}
		});
	}

	function assignClick(cell,key){
		var returnVal = assignMultiChoiceClick(cell,key);
		if(!returnVal){
			returnVal = assignTextEntryClick(cell,key);
		}
		return returnVal;
	}

	function assignMultiChoiceClick(cell,key){
		if(ChargeTable.multiChoiceFields[key] == undefined){
			return false;
		}

		var options = [["Filter By",function(cMenuDiv,clickable,optionVal){
										 cMenuDiv.hide();
										 myTable.makeFilter(key,data[key]);
										 myTable.getDBInfo(dbSearchCriteria);
									 }],
									 ["mark as:"]];

		fillMultiChoiceMenu(options,key,function(cMenuDiv,clickable,optionVal){
				cMenuDiv.hide();
				var assignVal = optionVal;
				//console.log("ASSIGN VAL",assignVal)
				if(assignVal === "(blank)"){
					assignVal = "";
				}
				$(cell).html(assignVal);
				updateKey(key,assignVal);
				myTable.autoFillAsk(key,myself);
			});

		contextMenu(cell,"#contextMenu",options);
		return true;
	}

	function assignTextEntryClick(cell,key){
			if(ChargeTable.textEntryFields[key] == undefined){
				return false;
			}

			var type = "input";
			if(key == "sentence"){
				type = "textarea";
			}

			options = [["Edit",function(cMenuDiv){
											cMenuDiv.hide();
											toggleTextField(cell,type,function(value){
												if(checkInputFormat(key,value)){
													updateKey(key,value);
													myTable.autoFillAsk(key,myself);
												}
												else{
													$(cell).text("");
												}
											});
										}],["Filter By",function(cMenuDiv,clickable,optionVal){
										//console.log("FILTER BY"+data[key]);
										 cMenuDiv.hide();
										 myTable.makeFilter(key,data[key]);
										 myTable.getDBInfo(dbSearchCriteria);
									 }]];

			contextMenu(cell,"#contextMenu",options);
			return true;
	}

	function addHighlightOptions(cell,columnName){
		var cell = $(cell);
		console.log(myTable);
		cell.attr("title",myTable.dressUpColumnName(columnName));

		cell.click(function(){
			if(_highlightKeyDown){
				if(cell.parent().children().hasClass("de_emphasize")){
					cell.parent().children().removeClass("de_emphasize");
				}
				else if(cell.parent().children().hasClass("emphasize")){
					cell.parent().children().removeClass("emphasize");
					cell.parent().children().addClass("de_emphasize");
				}
				else{
					cell.parent().children().addClass("emphasize");
				}
			}
		});
	}

function updateKey(key,value){
		data[key] = value;
		if(!entriesChanged[key]){
			if(initialData[key] != data[key]){
				entriesChanged[key] = true;
				myTable.cellChanged();
			}
		}
		else{
			if(initialData[key] == data[key]){
				entriesChanged[key] = false;
				myTable.cellChangedBack();
			}
		}
	}

	this.returnRow = function(){
		return myRow;
	}

	this.getCellValue = function(cell){
		if(cell == "prefixAndCaseNumber"){
			return data["prefix"]+data["caseNumber"];
		}
		else{
			return data[cell];
		}
	}

	this.setCellValue = function(cell, value){
		if(data.hasOwnProperty(cell) && cell != "rowID"){
			updateKey(cell,value);
			myCells[myTable.columnIndex(cell)].text(value);
		}
	}

	this.getIdentity = function(){
		return [data["prefix"],data["caseNumber"],data["rowID"]];
	}

	this.sendChanges = function(){
		var sendData = {};
		console.dir(sendData);

			Object.keys(entriesChanged).forEach(function(key){
			if(entriesChanged[key]){
					sendData[key] = initialData[key] = data[key];
					entriesChanged[key] = false;
					myTable.cellChangedBack();
			}
		});

		if(Object.keys(sendData).length != 0){
			//console.log("HERE!", sendData);
			$.ajax({url:"PHP/updateDB.php",
							method: "POST",
							data: {prefix: data["prefix"],
										 rowID: data["rowID"],
										 changes: JSON.stringify(sendData)},
							error: function(jqXHR,stat,er){alert("Huston, we have a problem. DB update failed:\n"+er);}
			});
		}
	}
}
