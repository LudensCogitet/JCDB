function DatabaseRow(rawData,rowArray){
	
	var multiChoiceFields = 	{"status":					["pndg","apld","hldg","clsd","(blank)"],
														 "verdict":					["ng", "g", "ni", "md", "wd", "(blank)"],
														 "sentenceStatus":	["impsd", "cmpl", "mrgd", "(blank)"]};
	
	var textEntryFields 	=		["hearingDate","sentence"];
	
	var entriesChanged = {"status": 				false,
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
	
		function assignFormDisplay(cell){
		 cell.click(function(){
			$.ajax({url:"displayCase.php",
					type: "POST",
					data: {"prefix": data["prefix"], 
								 "caseNum": data["caseNumber"]},
			success: function(result){
			  console.log("this is the result",result);
			  var complaintForm = new ComplaintForm(JSON.parse(result),"both",true);
			  console.log("lastFormData before:", localStorage.lastFormData);
			  localStorage.setItem('lastFormData',JSON.stringify(complaintForm.getData()));
			  console.log("lastFormData after:", localStorage.lastFormData);
			  $("#caseTarget").append(complaintForm.getJqueryElement("complete"));
			  $("#caseTarget").append($("<button>Add Hearing Notes</button>"));
			  $("#caseTarget").children("button").click(function(){
				window.location.href="addHearingNotes.html"});
			  console.log("caseScan",complaintForm.getData());
			  $("#scanTarget").append($(complaintForm.getData("formScan")));
			  $("#caseInfo").show();
			}});
		 });
  }
		
		function assignMultiChoiceClick(cell,key){
			
			var options = [["filter by:"],
										 [cell.innerHTML],
										 ["mark as:"]];
										 
			for(let i = 0; i < multiChoiceFields[key].length; i++){
				options.push([multiChoiceFields[key][i],function(cMenuDiv){
					cMenuDiv.hide();
					var assignVal = multiChoiceFields[key][i];
					if(assignVal === "(blank)"){
						assignVal = "";
					}
					$(cell).html(assignVal);
					updateKey(key,assignVal);
				}]);
			}
			
			contextMenu(cell,"#contextMenu",options);
		}
		
		function assignTextEntryClick(cell,key){		
				
				var type = null;
				if(key == "hearingDate"){
					type = "input";
				}
				else if(key == "sentence"){
					type = "textarea";
				}
				
				contextMenu(cell,"#contextMenu",[["Edit",function(cMenuDiv){
																									cMenuDiv.hide();
																									toggleTextField(cell,type,function(value){
																											updateKey(key,value);
																										});
																								}
																				 ]]);
		}
		
		var myRow = $("<tr>");
		var myCells = [$("<td>")];
	
		myCells[0].append(data["prefix"]+"-"+data["caseNumber"]);
		assignFormDisplay(myCells[0]);
		
		Object.keys(data).forEach(function(key){
			if(key === "prefix" || key == "caseNumber" || key == "rowID"){
				return;
			}
			else
			{
				myCells.push($("<td>"));
				var currentCell = myCells[myCells.length-1];
				
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
			$.ajax({url:"./updateDB.php",
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