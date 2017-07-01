var mainTable = new ChargeTable(document.getElementById("mainTable"));
$("#updateDBButton").click(function(){
  if(confirm('Are you sure?')){
    mainTable.sendChanges();
    $(this).hide();
  }
});

function windowClose(){
  $(this).parent().hide();
  $(this).siblings().children(":not(#updateCasebutton)").remove();
  localStorage.removeItem('lastFormData');
}

$("#caseInfoClose").click(windowClose);

$("#loadMore").click(function(){
  mainTable.loadMore();
});

function makeReport(type){
  mainTable.makeReport(type);
}

$("html").click(function(){
  $("#contextMenu").hide();
  $(".contextMenuStyle").not("#contextMenu").remove();
});
