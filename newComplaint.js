function reproduceField(event){
    if(event == "down" || event.keyCode == 40 && $(this).data("repro") == false){
      $(this).data("repro",true);
      console.log();
      var wholeName = $(this).attr("name");
      var name = wholeName.slice(0,wholeName.indexOf("-"));
      var num = parseInt(wholeName.substr(wholeName.indexOf("-")+1));
      console.log(name);
      console.log(num);
      var newField = $("<input type='text' name="+name+"-"+(num+1)+" data-repro='false' required>");
      console.log(newField);
      $(this).parent().append("<br>");
      $(this).parent().append(newField);
      $(newField).keydown(reproduceField);
      $(newField).focus();
	  return newField;
    }
   else if(event == "up" || event.keyCode == 38){
     var lastObj = $(this).prev().prev("input");
     var nextObj = $(this).next();
     if(lastObj.length != 0 && nextObj.length == 0){
       lastObj.data("repro",false);
       lastObj.next().remove();
       lastObj.focus();
       $(this).remove();
	   return lastObj;
     }
   }
  }