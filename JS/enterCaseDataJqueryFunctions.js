$(document).ready(function(){
  $("#showNotes").click(function(){
    if($('#caseNoteTarget').is(':hidden')){
      $('#caseNoteTarget').show();
      $(this).text('Hide Case Notes');
    }
    else{
      $('#caseNoteTarget').hide();
      $(this).text('Show Case Notes');
    }
  });

  $("#showContempts").click(function(){
    if($('#contemptTarget').is(':hidden')){
      $('#contemptTarget').show();
      $(this).text('Hide Contempt Charges');
    }
    else{
      $('#contemptTarget').hide();
      $(this).text('Show Contempt Charges');
    }
  });

  $(".deleteCaseNote").click(function(){
    var $me = $(this);
    if(confirm('Are you sure?')){
      $.ajax({url:"PHP/deleteCaseNote.php",
          type: "POST",
          data: {'rowID': $(this).data('rowid')},
          success: function(result){
            $me.parent().replaceWith('<div style="color: red"><b>DELETED</b></div>');
          }
      });
    }
  });

  $(".deleteContempt").click(function(){
    var $me = $(this);
    if(confirm('Are you sure?')){
      $.ajax({url:"PHP/deleteContempt.php",
          type: "POST",
          data: {'entryRowID': $me.data('entryrowid'),
                 'statusRowID': $me.data('statusrowid')},
          success: function(result){
            $me.parent().replaceWith('<div style="color: red"><b>DELETED</b></div>');
          }
      });
    }
  });

  $("#addContempt").click(function(){
    $("#newContemptTarget").prepend(
      "<div class='stackable'>"+
      "<table class='complaintTable'>"+
      "<thead><th>New Contempt Charge</th><th><div class='UIButton buttonSmall cancelContempt'> Cancel </div></th></thead>"+
      "<tbody>"+
      "<tr><td><b>Plaintiff</b></td><td><input required type='text' name='newContemptPlaintiff' value='JC'></input></td></tr>"+
      "<tr><td><b>Defendant</b></td><td><input required type='text' name='newContemptDefendant'></input></td></tr>"+
      "<tr><td><b>Charge</b></td><td><input type='radio' name='newContemptCharge' value='Contempt' checked>Contempt</input>"+
      "<input type='radio' name='newContemptCharge' value='Exile'>Exile</input>"+
      "</td></tr>"+
      "<tr><td><b>Date</b></td><td><input required readonly type='text' name='newContemptDate' value='"+new Date().toISOString().split("T")[0]+"'></input></td></tr>"+
      "</tbody>"+
      "</table>"+
      "</div>"
    );

    $(".cancelContempt").click(function(){
      console.log("got here");
      $(this).parent().parent().parent().parent().remove();
      $("#addContempt").show();
    });
    $(this).hide();
  });
});
