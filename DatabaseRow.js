function DatabaseRow(rawData,targetTable,rowArray){
	
	var multiChoiceFields = 	{"status":					["pndg","apld","hldg","clsd","(blank)"],
														 "verdict":					["ng", "g", "ni", "md", "wd", "(blank)"],
														 "sentenceStatus":	["impsd", "cmpl", "mrgd", "(blank)"]};
	
	var textEntryFields 	=		["hearingDate","sentence"];
	
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
	
	var data = JSON.parse(JSON.stringify(initialData));
	
	console.dir(initialData);
	console.dir(data);
	
		function assignFormDisplay(cell){
		 $(cell).click(function(){
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
			contextMenu = $("#contextMenu");
			
			$(cell).click(function(){
						contextMenu.append($("<div class='menuHeading'>filter by:</div>"));
						contextMenu.append($("<div class='menuOption'>"+cell.innerHTML+"</div>"));
						contextMenu.append($("<div class='menuHeading'>mark as:</div>"));
						
						for(let i = 0; i < multiChoiceFields[key].length; i++){
								var menuOption = $("<div class='menuOption'>"+multiChoiceFields[key][i]+"</div>");
								menuOption.click(function(){
									var assignVal = multiChoiceFields[key][i];
									if(assignVal === "(blank)"){
										assignVal = "";
									}
									$(cell).html(assignVal);
									data[key] = assignVal;
									contextMenu.hide();
								});
								contextMenu.append(menuOption);
						}
						contextMenu.show();
					});
		}
		
		function assignTextEntryClick(cell,key){		
				var inputType = "";
				var inputField = "";
				
				if(key == "hearingDate"){
						inputType = "input";
						inputField = $("<input type='text'></input>");
					}
					else if(key == "sentence"){
						inputType = "textarea";
						inputField = $("<textarea></textarea>");
					}
				
				$(cell).click(function(){
					contextMenu.hide();
					
					if($(this).children(inputType).length == 0){
						var inputVal = data[key];
						if(inputVal == null)
						inputVal = "";
					
						if(inputType == "input"){
							inputField.val(inputVal);
						}
						else if(inputType == "textarea"){
							inputField.append(inputVal);
						}
					
						$(this).html(inputField);
								
						$(this).children().keydown(function(event){
							if(event.keyCode == 13){
								var value = $(this).val();
								console.log(value);
								
								data[key] = value;
								$(this).parent().html(value);
							}		
						});
								
						$(this).children().focus();
						$(this).children().select();
					}
					else{
						var e = $.Event("keydown");
						e.keyCode = 13;
						$(this).children().trigger(e);
					}
				});
		}
		
		function statusFieldAction(){
			$(this).html();
		}
		
		var myRow = targetTable.insertRow(-1);
		var myCells = [myRow.insertCell(-1)];
	
		myCells[0].innerHTML = data["prefix"]+"-"+data["caseNumber"];
		assignFormDisplay(myCells[0]);
		
		Object.keys(data).forEach(function(key){
			if(key === "prefix" || key == "caseNumber" || key == "rowID"){
				return;
			}
			else
			{
				myCells.push(myRow.insertCell(-1));
				var currentCell = myCells[myCells.length-1];
				
				currentCell.innerHTML = data[key];
				
				var contextMenu = $("#contextMenu");
				
				$(currentCell).click(function(){
					contextMenu.css("left",event.pageX+"px");
					contextMenu.css("top",event.pageY+"px");
					contextMenu.empty();
					contextMenu.hide();
				});
				
				if(Object.keys(multiChoiceFields).indexOf(key) != -1){
					assignMultiChoiceClick(currentCell,key);
				}
				else if(textEntryFields.indexOf(key) != -1){
					assignTextEntryClick(currentCell,key);
				
				}
				else{
					$(currentCell).click(function(event){
						var content = $(this).html();
						content = content.split(", ");
						for(let i = 0; i < content.length; i++){
							content[i] = $("<div class='menuOption'>"+content[i]+"</div>");
							contextMenu.append(content[i]);
							contextMenu.show();
						}
					});
				 }
			}
		});

	this.getIdentity = function(){
		return [data["prefix"],data["caseNumber"],data["rowID"]];
	}

	this.getQueryString = function(){
		
	}
}