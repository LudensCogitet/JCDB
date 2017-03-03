function contextMenu(target,targetDiv,options){
	if(target != null)
		target = $(target);
	
	targetDiv = $(targetDiv);
	console.log("THIS HERE",options);
	
  function theBusiness(){
		targetDiv.empty();
	 
		for(let i = 0; i < options.length; i++){
			if(options[i].length == 1){
				targetDiv.append($("<div class='menuHeading'>"+options[i]+"</div>"));
			}
			else if(options[i].length == 2){
				var newOption = $("<div class='menuOption'>"+options[i][0]+"</div>");
    
				if(options[i][1] != null){
					newOption.click(function(event){
					event.stopPropagation();
					console.log("OPTIONS I 0",options[i][0])
					options[i][1](targetDiv,target,options[i][0]);
				});
			}
			targetDiv.append(newOption);
		}

	}
	targetDiv.css("left",event.pageX+"px");
	targetDiv.css("top",event.pageY+"px");
	targetDiv.show();
	console.log("AND THEN THIS",options);
	}
	
	if(target != null){
		target.click(function(event){
		event.stopPropagation();
		$(".contextMenuStyle").not("#contextMenu").remove();
		theBusiness();
		});
	}
	else{
		theBusiness();
	}
}

function toggleTextField(target,type,action){
	console.log("here");
		if($(target).children(type).length == 0){
						var inputVal = $(target).html();
						var inputField = null;
					
						if(type == "input"){
							inputField = $("<input type='text'></input>");
							inputField.val(inputVal);
						}
						else if(type == "textarea"){
							inputField = $("<textarea></textarea");
							inputField.append(inputVal);
						}
					
						$(target).html(inputField);
								
						$(target).children().keydown(function(event){
							if(event.keyCode == 13){
								var value = $(this).val();
								console.log(value);
								
								action(value);
								$(this).parent().html(value);
							}		
						});
								
						$(target).children().focus();
						$(target).children().select();
					}
					else{
						var e = $.Event("keydown");
						e.keyCode = 13;
						$(target).children().trigger(e);
					}	
}