function complaintForm(target, data = "new") {
	if (Array.isArray(data)){
			$.ajax({url:"PHP/caseForm.php",
					type: "POST",
					data: {"prefix": data[0],
							   "caseNumber": data[1],
                 "formScanButton": 'true',
                 "type": 'existing'},
					success: function(result){
            $(target).prepend(result);
					}
			});
		}
	else{
    $.ajax({url:"PHP/caseForm.php",
        type: "POST",
        data: {"type": 'new'},
        success: function(result){
          $(target).prepend(result);
        }
    });
	}
}
