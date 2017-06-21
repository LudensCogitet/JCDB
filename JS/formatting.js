function checkInputFormat(key,value){
	if(key == "hearingDate" || key == "dateOfIncident" || key == "date"){
		if(value.search(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/) == -1 && value.length != 0){
			alert("Please enter dates as YYYY-MM-DD");
			return false;
		}
		else{
			return true;
		}
	}
	else if(key == "charge"){
		value = value.toLowerCase();
		if(value.search(/^[A-Za-z ]+ [0-9]{6}$/) == -1 &&
			 value != "preamble" &&
			 value != "contempt" &&
			 value != "exile" &&
			 value.length != 0){
			alert("Please include the name of the charge followed by the section number");
			return false;
		}
		else{
			return true;
		}
	}
	else{
		return true;
	}
}

function reproduceField(event) {
	if (event == "down" || event.keyCode == 40 || event.keyCode == 13 && $(this).data("repro") == false) {
			event.preventDefault();
			$(this).data("repro", true);

			var name = $(this).attr("name");
			var newField = $("<input type='text' name='" + name + "' data-repro='false' required>");

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
			if(lastObj.length == 0){
				return;
			}
			else{
				var nextObjs = $(this).nextAll("input");
				if (nextObjs.length == 0) {
					lastObj.data("repro", false);
				}
			}
			lastObj.focus();
			$(this).remove();
			return lastObj;
		}
	}
}

function formatCheck(event){
	if(!checkInputFormat($(this).parent().attr('id'),$(this).val())){
		$(this).val("");
	}
}
