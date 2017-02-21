function contextMenu(target,targetDiv,options){
	target = $(target);
	targetDiv = $(targetDiv);
	
  target.click(function(event){
    event.stopPropagation();
		targetDiv.empty();
	 
		for(let i = 0; i < options.length; i++){
			if(options[i].length == 1){
				targetDiv.append($("<div class='menuHeading'>"+options[i]+"</div>"));
			}
			else if(options[i].length == 2){
				var newOption = $("<div class='menuOption'>"+options[i][0]+"</div>");
    
				if(options[i][1] != null){
					newOption.click(function(){
					targetDiv.hide();
					options[i][1]();
				});
			}
			targetDiv.append(newOption);	 
		}

	}
	targetDiv.css("left",event.pageX+"px");
	targetDiv.css("top",event.pageY+"px");
	targetDiv.show();
 });
}

function toggleTextField(target,type,action){
	$(target).click(function(){
		if($(this).children(type).length == 0){
						var inputVal = $(this).html();
						var inputField = null;
					
						if(type == "input"){
							inputField = $("<input type='text'></input>");
							inputField.val(inputVal);
						}
						else if(type == "textarea"){
							inputField = $("<textarea></textarea");
							inputField.append(inputVal);
						}
					
						$(this).html(inputField);
								
						$(this).children().keydown(function(event){
							if(event.keyCode == 13){
								var value = $(this).val();
								console.log(value);
								
								action(value);
								$(this).parent().html(value);
							}		
						});
								
						$(this).children().focus();
						$(this).children().select();
					}
					else{
						var e = $.Event("keydown");
						e.keyCode = 13;
						$(this).children().trigger(e);
					}
	});
	
}