var _highlightKeyDown = false;
$(window).keydown(function(event){if(event.which == 72)_highlightKeyDown = true;});
$(window).keyup(function(event){if(event.which == 72)_highlightKeyDown = false;});

function DatabaseRow(rawData,rowArray){

	var textEntryFields 	=		["hearingDate","sentence"];

	var entriesChanged = {"plaintiff":			false,
												"defendant":			false,
												"witness":				false,
												"charge":					false,
												"status": 				false,
												"verdict": 				false,
												"sentenceStatus":	false,
												"hearingDate": 		false,
												"sentence": 			false};

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
		"rowID":					rawData[11]};

	Object.keys(initialData).forEach(function(key){
		if(initialData[key] == null){
			initialData[key] = "";
		}
	});

	var data = JSON.parse(JSON.stringify(initialData));

	var myRow = $("<tr>");
	var myCells = [$("<td>")];

	myCells[0].append(data["prefix"]+"-"+data["caseNumber"]);
	myCells[0].click(function(){
		complaintForm("#caseTarget",[data['prefix'],data['caseNumber']],"both","both",true);
		$("#updateComplaintForm").append("<input type='hidden' name='prefix' value='"+data['prefix']+"'>");
		$("#updateComplaintForm").append("<input type='hidden' name='caseNumber' value='"+data['caseNumber']+"'>");
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
			if(hearingFields[key]){
				console.log("ADDING PDNGINVIS");
				currentCell.addClass("pndgInvis");
				currentCell.addClass(key);
			}

			addHighlightOptions(currentCell,key);

			currentCell.append(data[key]);

			if(Object.keys(multiChoiceFields).indexOf(key) != -1){
				assignMultiChoiceClick(currentCell,key);
			}
			else if(textEntryFields.indexOf(key) != -1){
				assignTextEntryClick(currentCell,key);
			}
		}
	});

	for(let i = 0; i < myCells.length; i++)
		myRow.append(myCells[i]);

	function updateKey(key,value){
			var updateButton = $("#updateDBButton");
			data[key] = value;
			if(!entriesChanged[key]){
				if(initialData[key] != data[key]){
					entriesChanged[key] = true;
					DatabaseRow.numChanged++;
					console.log(DatabaseRow.numChanged);
					if(!updateButton.is(":visible")){
						updateButton.show();
					}
				}
			}
			else{
				if(initialData[key] == data[key]){
					entriesChanged[key] = false;
					DatabaseRow.numChanged--;
					if(DatabaseRow.numChanged == 0){
						updateButton.hide();
				}
			}
		}
	}

	function addHighlightOptions(cell,columnName){
		var cell = $(cell);

		cell.attr("title",dressUpColumnName(columnName));

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

	function autoFillAsk(cell,key){
		var myCaseNumber = (data["prefix"]+data["caseNumber"]).toString();
		var myCase = rowObjects["caseNumber"][myCaseNumber];

		var doAsk = true;
		if(key == 'sentence'){
			doAsk = false;
		}
		else if(myCaseNumber in DatabaseRow.autoFillDoNotAsk){
			if(DatabaseRow.autoFillDoNotAsk[myCaseNumber].indexOf(key) != -1)
				doAsk = false;
		}

		if(doAsk && myCase.length > 1){
			console.log(key, "THIS IS THE KEY");
			var newMenu = $("<div class='contextMenuStyle'>");
			$("html").append(newMenu);

			var fillWith = key == 'sentenceStatus' && data[key] == 'impsd' ? 'mrgd' : data[key];

			var options = [["Auto-fill "+dressUpColumnName(key)+" with \""+fillWith+"\" for this case?"],
										["Yes",	function(cMenuDiv,clickable,optionVal){
															for(let i = 0; i < myCase.length; i++){
																if(myCase[i].getCellValue(key) != data[key])
																	myCase[i].setCellValue(key,fillWith);
															}
															$("#contextMenu").hide();
															cMenuDiv.remove();
														}],
										["No",function(cMenuDiv,clickable,optionVal){
															if(!(myCaseNumber in DatabaseRow.autoFillDoNotAsk)){
																console.log("HIIIIII!")
																DatabaseRow.autoFillDoNotAsk[myCaseNumber] = [];
															}

															DatabaseRow.autoFillDoNotAsk[myCaseNumber].push(key);
															$("#contextMenu").hide();
															cMenuDiv.remove();
														}]];
			contextMenu(null,newMenu,options);
		}
	}

	function assignMultiChoiceClick(cell,key){

		var options = [["Filter By",function(cMenuDiv,clickable,optionVal){
										 cMenuDiv.hide();
										 makeFilter(key,data[key]);
										 getDBInfo(dbSearchCriteria);
									 }],
									 ["mark as:"]];

		fillMultiChoiceMenu(options,key,function(cMenuDiv,clickable,optionVal){
				cMenuDiv.hide();
				var assignVal = optionVal;
				console.log("ASSIGN VAL",assignVal)
				if(assignVal === "(blank)"){
					assignVal = "";
				}
				$(cell).html(assignVal);
				updateKey(key,assignVal);
				autoFillAsk(cell,key);
			});

		contextMenu(cell,"#contextMenu",options);
	}

	function assignTextEntryClick(cell,key){

			var type = "input";
			if(key == "sentence"){
				type = "textarea";
			}

			options = [["Edit",function(cMenuDiv){
											cMenuDiv.hide();
											toggleTextField(cell,type,function(value){
												if(checkInputFormat(key,value)){
													updateKey(key,value);
													autoFillAsk(cell,key);
												}
												else{
													$(cell).text("");
												}
											});
										}],["Filter By",function(cMenuDiv,clickable,optionVal){
										console.log("FILTER BY"+data[key]);
										 cMenuDiv.hide();
										 makeFilter(key,data[key]);
										 getDBInfo(dbSearchCriteria);
									 }]];

			contextMenu(cell,"#contextMenu",options);
	}

	this.returnRow = function(){
		console.log(myRow);
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
			myCells[columnIndex[cell]].text(value);
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
					DatabaseRow.numChanged--;
					if(DatabaseRow.numChanged == 0){
						$("#updateDBButton").hide();
				}
			}
		});

		if(Object.keys(sendData).length != 0){
			console.log("HERE!", sendData);
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

DatabaseRow.numChanged = 0;
DatabaseRow.autoFillDoNotAsk = [];
