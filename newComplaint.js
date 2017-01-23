function reproduceField(event){
    if(event.keyCode == 40 && $(this).data("repro") == false){
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
    }
   else if(event.keyCode == 38){
     var lastObj = $(this).prev().prev("input");
     var nextObj = $(this).next();
     if(lastObj.length != 0 && nextObj.length == 0){
       lastObj.data("repro",false);
       lastObj.next().remove();
       lastObj.focus();
       $(this).remove();
     }
   }
  }

$(document).ready(function(){
  $("input[type='text']").keydown(reproduceField);
});