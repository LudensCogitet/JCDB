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
	
	function assignFormDisplay(cell){
		cell.click(function(){
		$.ajax({url:"../PHP/displayComplaint.php",
				type: "POST",
				data: {"prefix": data["prefix"], 
							 "caseNum": data["caseNumber"]},
		success: function(result){
		  console.log("this is the result",result);
		  var complaintForm = new ComplaintForm(JSON.parse(result),"both",true);
		  //console.log("lastFormData before:", localStorage.lastFormData);
		  localStorage.setItem('lastFormData',JSON.stringify(complaintForm.getData()));
		  //console.log("lastFormData after:", localStorage.lastFormData);
		  $("#caseTarget").append(complaintForm.getJqueryElement("complete"));
		  $("#caseTarget").append($("<button id='updateComplaint'>Update complaint</button>"));
		  
			$("#caseTarget").children("#updateComplaint").click(function(){
				window.location.href="../PHP/enterComplaintData.php"
			});
		  //console.log("caseScan",complaintForm.getData());
		  
			var scanDisplayForm = "<form target='_blank' action='../PHP/scanDisplay.php' type='post'>"+
														"<input type='hidden' name='scanSrc' value='"+complaintForm.getData("formScan")+"'>"+
														"<input type='submit' value='Open complaint form scan'></submit>";
			
			$("#caseTarget").append(scanDisplayForm);
		  $("#caseInfo").show();
		}});
	 });
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
			});
			
		contextMenu(cell,"#contextMenu",options);
	}
		
	function assignTextEntryClick(cell,key){		
				
			var type = "input";
			if(key == "sentence"){
				type = "textarea";
			}
			
			options = [["Filter By",function(cMenuDiv,clickable,optionVal){
										console.log("FILTER BY"+data[key]);
										 cMenuDiv.hide();
										 makeFilter(key,data[key]);
										 getDBInfo(dbSearchCriteria);
									 }],
									 ["Edit",function(cMenuDiv){
											cMenuDiv.hide();
											toggleTextField(cell,type,function(value){
												updateKey(key,value);
											});
										}]];
			
			contextMenu(cell,"#contextMenu",options);
	}
		
	var myRow = $("<tr>");
	var myCells = [$("<td>")];
	
	myCells[0].append(data["prefix"]+"-"+data["caseNumber"]);
	assignFormDisplay(myCells[0]);
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
		
	this.returnRow = function(){
		console.log(myRow);
		return myRow;
	}
	
	this.getCellValue = function(cell){
		return data[cell];
	}
	
	this.setCellValue = function(cell, value){
		if(data.hasOwnProperty(cell) && cell != "rowID"){
			data[cell] = value;
			myCells[columnIndex[cell]].html(value);
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
			$.ajax({url:"../PHP/updateDB.php",
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