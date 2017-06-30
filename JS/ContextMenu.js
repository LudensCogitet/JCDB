function contextMenu(target,targetDiv,options, center = false){
	if(target != null)
		target = $(target);

	targetDiv = $(targetDiv);
	//console.log("THIS HERE",options);

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
					//console.log("OPTIONS I 0",options[i][0])
					options[i][1](targetDiv,target,options[i][0]);
				});
			}
			targetDiv.append(newOption);
		}

	}

	var offsetX;
	var offsetY;
	if(event.pageX !== undefined && event.pageY !== undefined && center == false){
		//console.log("CENTER", center);
		offsetX = event.pageX;
		offsetY = event.pageY;
	}
	else{
		offsetX = $(window).width() / 2 - targetDiv.width() / 2;
		offsetY = $(window).height() / 2 - targetDiv.height()/2;
	}

	targetDiv.css("left",offsetX+"px");
	targetDiv.css("top",offsetY+"px");
	targetDiv.show();
	//console.log("AND THEN THIS",options);
	}

	if(target != null){
		target.click(function(event){
			if(_highlightKeyDown == false){
				event.stopPropagation();
				$(".contextMenuStyle").not("#contextMenu").remove();
				theBusiness();
			}
		});
	}
	else{
		theBusiness();
	}
}

function toggleTextField(target,type,action){
	//console.log("here");
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
								//console.log(value);

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

function fillMultiChoiceMenu(options,key,funcs){
	if(!Array.isArray(funcs)){
		for(let i = 0; i < ChargeTable.multiChoiceFields[key].length; i++){
			options.push([ChargeTable.multiChoiceFields[key][i],funcs]);
		}
	}
	else{
		for(let i = 0; i < ChargeTable.multiChoiceFields[key].length; i++){
			options.push([ChargeTable.multiChoiceFields[key][i],funcs[i]]);
		}
	}
}
