function checkInputFormat(key,value){
	if(key == "hearingDate" || key == "dateOfIncident"){
		if(value.search(/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/) == -1 && value.length != 0){
			alert("Please enter dates as YYYY-MM-DD");
			return false;
		}
		else{
			return true;
		}
	}
	else if(key == "charge"){
		if(value.search(/^[A-Za-z ]+ [0-9]{6}$/) == -1 && value.toLowerCase() != "preamble" && value.length != 0){
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