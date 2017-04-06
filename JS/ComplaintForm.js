function complaintForm(target, info = "new", readOnly = false) {
  var multiFields = ["plaintiff","defendant","witness","charge","dateOfIncident","timeOfIncident","location","hearingDate"];
	
	var data = null;
	if (Array.isArray(info)) {
		$.ajax({url:"../PHP/displayComplaint.php",
				type: "POST",
				data: {"prefix": info[0], 
						 "caseNum": info[1]},
				success: function(result){
					result = JSON.parse(result)
					
					for(let i = 0; i < multiFields.length; i++){
						if(typeof result[multiFields[i]] === "string"){
							result[multiFields[i]] = result[multiFields[i]].split(", ");
						}
					}
 
					if(result["hearingDate"] == null)
						result["hearingDate"] = [];
 
					if(result["hearingNotes"] == null)
						result["hearingNotes"] = "";
					
					data = result;
					theBusiness();
				}
		});
  }
	else{
		theBusiness();
	}

	function theBusiness(){
		var jqueryElement = null;
		var jqueryInputFields = null;

		var returnString = compileString();

		jqueryElement = $(returnString);
		jqueryInputFields = jqueryElement.children().children().children();
		jqueryInputFields.children("input[type='text']").keydown(reproduceField);
		jqueryInputFields.children().blur(formatCheck);
		setReadOnly(readOnly,jqueryInputFields);

		$(target).prepend(jqueryElement);
		var scanDisplayForm = "<form name='viewScanButton' target='_blank' action='../PHP/scanDisplay.php' type='post'>"+
													"<input type='hidden' name='scanSrc' value='"+data["formScan"]+"'></form>"+
													"<div style='float:right' class='UIButton buttonLong' onclick='document.viewScanButton.submit();'>View Complaint Scan</div>";
				
		$(target).append(scanDisplayForm);
	}
	
	function makeInputFields(typeOfData) {
		var coda = '" required></input>';
		if(typeOfData == "witness" || typeOfData == "hearingDate")
			coda = '"></input>';
		
		var returnString = '<input type="text" name="' + typeOfData + '-1" data-repro="false" value="'+data[typeOfData]+ coda;
		
    if(data != "new"){
			if (Array.isArray(data[typeOfData]) && data[typeOfData].length > 0) {
				var repro = "true";

				for (let i = 0; i < data[typeOfData].length; i++) {
					if (i == data[typeOfData].length - 1)
						repro = "false";
				
					returnString += '<input type="text" name="' + typeOfData + '-' + (i + 1) + '" data-repro="' + repro + '" value="' + data[typeOfData][i] + coda;
				}
			}
		}
    return returnString;
  }

  function reproduceField(event) {
    if (event == "down" || event.keyCode == 40 || event.keyCode == 13 && $(this).data("repro") == false) {
				event.preventDefault();
				$(this).data("repro", true);
				console.log();
				var wholeName = $(this).attr("name");
				var name = wholeName.slice(0, wholeName.indexOf("-"));
				var num = parseInt(wholeName.substr(wholeName.indexOf("-") + 1));
				console.log(name);
				console.log(num);
				var newField = $("<input type='text' name=" + name + "-" + (num + 1) + " data-repro='false' required>");
				console.log(newField);
				$(this).parent().append(newField);
				$(newField).keydown(reproduceField);
				$(newField).blur(formatCheck);
				$(newField).focus();
				return newField;
			} 
			else if (event == "up" || event.keyCode == 38 || (event.keyCode == 8 && $(this).val() == "")) {
			if($(this).attr('readonly') != 'readonly'){
				event.preventDefault();
				var lastObj = $(this).prev("input");
				var nextObj = $(this).next("input");
				if (lastObj.length != 0 && nextObj.length == 0) {
					lastObj.data("repro", false);
					lastObj.focus();
					$(this).remove();
					return lastObj;
				}
			}
		}
	}
	
	function formatCheck(event){
		if(!checkInputFormat($(this).parent().attr('id'),$(this).val())){
			$(this).val("");
		}
	}

  function compileString() {

    var complaintString = '<table class="complaintTable" id="mainComplaint">';
		
		if(data["prefix"] != ""){	
			complaintString += '<input type="hidden" value="'+data["prefix"]+'" name="prefix"'+'></input>';
		}
			
		if(data["caseNumber"] != ""){
			complaintString +=	'<input type="hidden" value="'+data["caseNumber"]+'" name="caseNumber"'+'></input>';
		}
			
		if(data["formScan"] != ""){
			complaintString += 	'<input type="hidden" value="'+data["formScan"]+'" name="formScan"'+'></input>';
		}
			
		complaintString += '<tr>' +
      '<th>Case No.</th>' +
      '<td class="textField" id="caseNumber">' + data["prefix"] + '-' + data["caseNumber"] + '</td>' +
      '</tr>' +
      '<tr>' +
      '<th>Plaintiff</th>' +
      '<td class="textField" id="plaintiff">' + makeInputFields("plaintiff") + '</td>' +
      '<th>Date of Incident (YYYY-MM-DD)</th>' +
      '<td class="textField" id="dateOfIncident">' + makeInputFields("dateOfIncident") + '</td>' +
      '</tr>' +
      '<tr>' +
      '<th>Defendant</th>' +
      '<td class="textField" id="defendant">' + makeInputFields("defendant") + '</td>' +
      '<th>Time of Incident</th>' +
      '<td class="textField" id="timeOfIncident">' + makeInputFields("timeOfIncident") + '</td>' +
      '</tr>' +
      '<tr>' +
      '<th>Witness</th>' +
      '<td class="textField" id="witness">' + makeInputFields("witness") + '</td>' +
      '<th>Location</th>' +
      '<td class="textField" id="location">' + makeInputFields("location") + '</td>' +
      '</tr>' +
      '<tr>' +
      '<th>What happened</th>' +
      '<td class="areaField" id="whatHappened"><textarea name="whatHappened" required>' + data["whatHappened"] + '</textarea></td>' +
      '<th>Charge & Sec. Number</th>' +
      '<td class="textField" id="charge">' + makeInputFields("charge") + '</td>' +
      '</tr>' +
      '</table>';

    if(data["hearingDate"] != [] || data["hearingNotes"] != ""){
			complaintString += '<table class="complaintTable" id="extraComplaint">' +
				'<input type="hidden" value="'+data["prefix"]+'" name="prefix"'+'></input>'+
				'<input type="hidden" value="'+data["caseNumber"]+'" name="caseNo"'+'></input>'+
				'<tr>' +
				'<th>Hearing Date (YYYY-MM-DD)</th>' +
				'<td id="hearingDate" style="width: 767px;">' + makeInputFields("hearingDate") + '</td>' +
				'</tr>' +
				'<tr>' +
				'<th>Hearing Notes</th>' +
				'<td class="areaField" id="hearingNotes" style="width: 767px;"><textarea name="hearingNotes">' + data["hearingNotes"] + '</textarea></td>' +
				'</tr>' +
				'</table>';
		}
		return complaintString;
	}

  function setReadOnly(val,jqueryInputFields) {
    if (val == "top") {
        jqueryInputFields.children("#mainComplaint input[type='text']").attr("readonly", "readonly");
        jqueryInputFields.children("#mainComplaint textarea").attr("readonly", "readonly");
    }
	  else if(val == "both"){
		jqueryInputFields.children("input[type='text']").attr("readonly", "readonly");
        jqueryInputFields.children("textarea").attr("readonly", "readonly");
	  }
	  else {
        jqueryInputFields.children("input[type='text']").removeAttr("readonly");
        jqueryInputFields.children("textarea").removeAttr("readonly");
    }
  }
  
	this.markReadOnly = function(val) {
    readOnly = val;
  }
}