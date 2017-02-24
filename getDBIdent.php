<?php

function getDBIdent($dateString = "today"){
	
	$date = new DateTime();
	$date->setTimeStamp(strtotime($dateString));
	
	$firstDay = new DateTime();
	$firstDay->setTimeStamp(strtotime("first tuesday of september"));
	
	if($date->diff($firstDay)->invert == 0){
		return (DATE("y")-1).DATE("y");
	}
	else{
		return DATE("y").(DATE("y")+1);
	}
}
?>