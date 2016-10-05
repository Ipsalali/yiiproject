$(function(){

	$("#search").focusin(function(){
		$(this).trigger("keyup");
	})

	$("#search").keyup(function(){

		if($(this).val().length < 3){
			$(".search_result").css("display","none")
			return false;
		}
	 	
    	$.ajax({
      		url: 'index.php?r=site/search',
      		type:"POST",
      		data:'keywords=' +  $(this).val(),
      		dataType: 'json',
      		before_send:function(){},     
      		success: function(json) {
				
				if(json.length==0){
					$(".search_result").css("display","none")
					return false;
				}

				var html = json.html;
				$(".search_result").html(html);
			  	$(".search_result").css("display","block");
				
      		},
      		error:function(){},
      		complete:function(){}
    	});
	})

	$('#search').focusout(function(){
		setTimeout(function(){$(".search_result").css("display","none")},800);
  	});

})