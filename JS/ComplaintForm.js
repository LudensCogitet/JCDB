function complaintForm(target, data = "new", readOnly = false, formDisplayButton = false) {
  var multiFields = ["plaintiff","defendant","witness","charge","dateOfIncident","timeOfIncident","location"];

	if (Array.isArray(data)) {
			$.ajax({url:"PHP/displayCase.php",
					type: "POST",
					data: {"prefix": data[0],
							   "caseNum": data[1],
                 "complaint": true},
					success: function(result){
            console.log("displayCase return",result);
						theBusiness(result);
					}
			});
		}
		else if(data != "new"){
			theBusiness(data);
		}
	else{
		theBusiness();
	}

	function theBusiness(workWith = null){
		if(workWith != null){
			workWith = JSON.parse(workWith)
			workWith = convertFields(workWith);

			data = workWith;
		}

		var jqueryElement = null;
		var jqueryInputFields = null;

		var returnString = compileString();

		jqueryElement = $(returnString);
		jqueryInputFields = jqueryElement.children().children().children();
		jqueryInputFields.children("input[type='text']").keydown(reproduceField);
		jqueryInputFields.children().blur(formatCheck);
		setReadOnly(jqueryInputFields);

		$(target).prepend(jqueryElement);
		if(data != "new" && formDisplayButton == true){
			var scanDisplayForm = "<form name='viewScanButton' target='_blank' action='scanDisplay.php' type='post'>"+
														"<input type='hidden' name='scanSrc' value='"+data["formScan"]+"'></form>"+
														"<div class='UIButton buttonLong' onclick='document.viewScanButton.submit();'>View Complaint Scan</div>";

			$(target).append(scanDisplayForm);
		}
	}

	function convertFields(object){
		for(let i = 0; i < multiFields.length; i++){
					if(typeof object[multiFields[i]] === "string"){
						object[multiFields[i]] = object[multiFields[i]].split(", ");
					}
				}

				if(object["prefix"] == -1)
					object["prefix"] = "";

				if(object["caseNumber"] == -1)
					object["caseNumber"] = "";

		return object;
	}

	function makeInputFields(typeOfData) {
		var coda = '" required></input>';

		if(typeOfData == "witness")
			coda = '"></input>';

		var emptyInput = '<input type="text" name="' + typeOfData + '-1" data-repro="false"'+coda;

    if(data != "new"){
			if (Array.isArray(data[typeOfData]) && data[typeOfData].length > 0) {
				var repro = "true";
				var returnString = "";
				for (let i = 0; i < data[typeOfData].length; i++) {
					if (i == data[typeOfData].length - 1)
						repro = "false";

					returnString += '<input type="text" name="' + typeOfData + '-' + (i + 1) + '" data-repro="' + repro + '" value="' + data[typeOfData][i] + coda;
				}
				return returnString;
			}
			else{
				return emptyInput;
			}
		}
		else{
			return emptyInput;
		}
  }

  function reproduceField(event) {
    if (event == "down" || event.keyCode == 40 || event.keyCode == 13 && $(this).data("repro") == false) {
				event.preventDefault();
				$(this).data("repro", true);
				//console.log();
				var wholeName = $(this).attr("name");
				var name = wholeName.slice(0, wholeName.indexOf("-"));
				var num = parseInt(wholeName.substr(wholeName.indexOf("-") + 1));
				//console.log(name);
				//console.log(num);
				var newField = $("<input type='text' name=" + name + "-" + (num + 1) + " data-repro='false' required>");
				//console.log(newField);
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

		var prefix = "";
		var caseNumber = "";
		var whatHappened = "";

		if(data != "new"){
			if(data["prefix"] != ""){
				prefix = data["prefix"];
				complaintString += '<input type="hidden" value="'+data["prefix"]+'" name="prefix"'+'></input>';
			}

			if(data["caseNumber"] != ""){
				caseNumber = data["caseNumber"];
				complaintString += '<input type="hidden" value="'+data["caseNumber"]+'" name="caseNumber"'+'></input>';
			}

			if(data["formScan"] != ""){
				complaintString += '<input type="hidden" value="'+data["formScan"]+'" name="formScan"'+'></input>';
			}

			if(data["whatHappened"] != ""){
				whatHappened = data["whatHappened"];
			}
		}

		complaintString += '<tr>' +
      '<th>Case No.</th>' +
      '<td class="textField" id="caseNumber">' + prefix + '-' + caseNumber + '</td>' +
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
      '<td class="areaField" id="whatHappened"><textarea name="whatHappened" required>' + whatHappened + '</textarea></td>' +
      '<th>Charge & Sec. Number</th>' +
      '<td class="textField" id="charge">' + makeInputFields("charge") + '</td>' +
      '</tr>' +
      '</table>';

    return complaintString;
	}

  function setReadOnly(jqueryInputFields) {
    if(readOnly == true){
      jqueryInputFields.children("#mainComplaint input[type='text']").attr("readonly", "readonly");
      jqueryInputFields.children("#mainComplaint textarea").attr("readonly", "readonly");
    }
  }

	this.markReadOnly = function(val) {
    readOnly = val;
  }
}
