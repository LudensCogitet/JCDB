var dbSearchCriteria = {};

var dataSet = [];
var rowObjects = {"array": [],
                  "caseNumber": {}};
var limits = {"offset": 0,
              "count": 30};

var upArrow = $("<span class='arrow up noPrint'>&#x25B2;</span>");
var downArrow = $("<span class='arrow down noPrint'>&#x25BC;</span>");

function getDBInfo(criteria = "all", type = "overwrite", myLimits = limits){
    DatabaseRow.autoFillDoNotAsk = [];
    return new Promise(function(resolve,reject){
    var check = false;
    if(typeof criteria == "object"){
      if(Object.keys(criteria).length == 0)
        criteria = "all"
    }

    if(type == "overwrite"){
      limits['offset'] = 0;
    }

    $.ajax({url:"PHP/index/returnDBInfo.php",
      method: "GET",
      data:{"criteria": JSON.stringify(criteria),
            "limits": JSON.stringify(limits)},
      success: function(result){
        if(result.search(/^error/i) != -1){
          console.log(result);
        }
        result = JSON.parse(result);
        if(result[0] == true){
          $("#loadMore").show();
        }
        else{
          $("#loadMore").hide();
        }

        dataSet = result[1];

        if(type == "overwrite"){
          rowObjects = makeTable(dataSet);
        }
        else{
          rowObjects = makeTable(dataSet,rowObjects);
        }
        console.log(rowObjects);
        sortRows();
        fillTable(mainTable.tBodies[0],type);
        resolve("Yay");
    }});
  });
}

var mainTable;

$(document).ready(function(){
  mainTable = document.getElementById("mainTable");

  headingMenuSetup("prefixAndCaseNumber");
  headingMenuSetup("plaintiff");
  headingMenuSetup("defendant");
  headingMenuSetup("witness");
  headingMenuSetup("charge");
  headingMenuSetup("status");
  headingMenuSetup("hearingDate");
  headingMenuSetup("verdict");
  headingMenuSetup("sentence");
  headingMenuSetup("sentenceStatus");
  headingMenuSetup("notes");

  $("#updateDBButton").click(function(){
    if(confirm('Are you sure?')){
      for(let i = 0; i < rowObjects["array"].length; i++){
        rowObjects["array"][i].sendChanges();
      }
      $(this).hide();
    }
  });

  getDBInfo();

  function windowClose(){
    $(this).parent().hide();
    $(this).siblings().children(":not(#updateCasebutton)").remove();
    localStorage.removeItem('lastFormData');
  }

  $("#caseInfoClose").click(windowClose);

  $("html").click(function(){
    $("#contextMenu").hide();
    $(".contextMenuStyle").not("#contextMenu").remove();
  });
});
