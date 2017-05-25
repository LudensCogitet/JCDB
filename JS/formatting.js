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
