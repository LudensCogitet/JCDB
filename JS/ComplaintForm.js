function ComplaintForm(info = "new", readOnly = false,convertFromString = false) {
  var arrayFields = ["plaintiff","defendant","witness","charge","dateOfIncident","timeOfIncident","location","hearingDate"];

  var data = null;
  if (info == "new") {
    data = {
      "formScan": "",
      "prefix": "",
      "caseNumber": "",
      "plaintiff": [],
      "defendant": [],
      "witness": [],
      "charge": [],
      "dateOfIncident": [],
      "timeOfIncident": [],
      "location": [],
      "whatHappened": "",
      "hearingDate": [],
      "hearingNotes": ""
    };
  } 
  else if (typeof info === "object") {
	  if(convertFromString){
		for(let i = 0; i < arrayFields.length; i++){
			if(typeof info[arrayFields[i]] === "string"){
				info[arrayFields[i]] = info[arrayFields[i]].split(", ");
		 }
		}
	 }
	 
		if(info["hearingDate"] == null)
			info["hearingDate"] = "";
	 
		if(info["hearingNotes"] == null)
			info["hearingNotes"] = "";
	 data = info; 
   }
  else{
	  
  }

  var mainComplaintString = "";
  var extraComplaintString = "";
  var jqueryElement = null;
  var jqueryInputFields = null;

  function makeInputFields(typeOfData) {
    var returnString = "";
    if (Array.isArray(data[typeOfData]) && data[typeOfData].length > 0) {

      var repro = "true";

      for (let i = 0; i < data[typeOfData].length; i++) {
        if (i == data[typeOfData].length - 1)
          repro = "false";

        returnString += '<input type="text" name="' + typeOfData + '-' + (i + 1) + '" data-repro="' + repro + '" value="' + data[typeOfData][i] + '" required></input>';
      }
    } else {
      returnString = '<input type="text" name="' + typeOfData + '-1" data-repro="false" value="'+data[typeOfData]+'" required></input>';
    }
    return returnString;
  }

  function reproduceField(event) {
    if (event == "down" || event.keyCode == 40 && $(this).data("repro") == false) {
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
				$(newField).focus();
				return newField;
			} 
			else if (event == "up" || event.keyCode == 38) {
			if($(this).attr('readonly') != 'readonly'){
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

  function compileStrings() {

    mainComplaintString = '<table class="complaintTable" id="mainComplaint">';
		
		if(data["prefix"] != ""){	
			mainComplaintString += '<input type="hidden" value="'+data["prefix"]+'" name="prefix"'+'></input>';
		}
			
		if(data["caseNumber"] != ""){
			mainComplaintString +=	'<input type="hidden" value="'+data["caseNumber"]+'" name="caseNumber"'+'></input>';
		}
			
		if(data["formScan"] != ""){
			mainComplaintString += 	'<input type="hidden" value="'+data["formScan"]+'" name="formScan"'+'></input>';
		}
			
		mainComplaintString += '<tr>' +
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

    extraComplaintString = '<table class="complaintTable" id="extraComplaint">' +
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

  this.updateFromJquery = function() {
    if (jqueryElement != null) {
      data["plaintiff"] = [];
      jqueryInputFields.filter("#plaintiff").children("input").each(function(index) {
        data["plaintiff"].push($(this).val());
      });

      data["defendant"] = [];
      jqueryInputFields.filter("#defendant").children("input").each(function(index) {
        data["defendant"].push($(this).val());
      });

      data["witness"] = [];
      jqueryInputFields.filter("#witness").children("input").each(function(index) {
        data["witness"].push($(this).val());
      });

      data["charge"] = [];
      jqueryInputFields.filter("#charge").children("input").each(function(index) {
        data["charge"].push($(this).val());
      });

      data["dateOfIncident"] = [];
      jqueryInputFields.filter("#dateOfIncident").children("input").each(function(index) {
        data["dateOfIncident"].push($(this).val());
      });

      data["timeOfIncident"] = [];
      jqueryInputFields.filter("#timeOfIncident").children("input").each(function(index) {
        data["timeOfIncident"].push($(this).val());
      });

      data["location"] = [];
      jqueryInputFields.filter("#location").children("input").each(function(index) {
        data["location"].push($(this).val());
      });

      data["whatHappened"] = jqueryInputFields.filter("#whatHappened").children().val();
			
			data["hearingDate"] = [];
      jqueryInputFields.filter("#hearingDate").children("input").each(function(index) {
        data["hearingDate"].push($(this).val());
      });
     
      data["hearingNotes"] = jqueryInputFields.filter("#hearingNotes").children().val();
 
      console.log(data);
      return true;
    } else {
      return false;
    }
  }

  function setReadOnly(val) {
    if (jqueryInputFields) {
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
  }
  this.markReadOnly = function(val) {
    readOnly = val;
  }

  this.getJqueryElement = function(type = "simple") {
    compileStrings();
    var returnString = mainComplaintString;

    if (type == "complete")
      returnString += extraComplaintString;

    jqueryElement = $(returnString);
    jqueryInputFields = jqueryElement.children().children().children();
    jqueryInputFields.children("input[type='text']").keydown(reproduceField);
    setReadOnly(readOnly);

    console.log(data);
    console.log(jqueryInputFields.filter("#plaintiff").children());
    return jqueryElement;
  }

  this.getData = function(field = "all") {
    if (field == "all") {
      return data;
    } else {
      if (data.hasOwnProperty(field)) {
        return data[field];
      } else
        return null;
    }
  }

  this.setData = function(field, newData) {
    if (data.hasOwnProperty(field)) {
      if (Array.isArray(data[field]) && Array.isArray(newData) || !Array.isArray(data[field]) && !Array.isArray(newData)) {
        data[field] = newData;
      }
    }
  }
}

/*$(document).ready(function() {
  var thing = new ComplaintForm();

  $("#target").append(thing.getJqueryElement("complete"));

  $("#update").click(function() {
    thing.updateFromJquery();
    console.log(thing.getData());
  });
  
  thing.setData("whatHappened","lol");
  console.log(thing.getData());
});*/