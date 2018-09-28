<div class="row search_block">
    <div class="col-md-12">
        <input type="text" id="search" name="search" class="form-control" placeholder="Поиск" autocomplete="off" />
        <div class="search_result"></div>
    </div>
</div>
<?php

$script = <<<JS

	$("#search").focusin(function(){
		$(this).trigger("keyup");
	})

	var send_req = 0;
	$("#search").keyup(function(){

		if($(this).val().length < 3){
			$(".search_result").css("display","none");
			return false;
		}
	 	
	 	if(!send_req){
	 		$.ajax({
	      		url: 'index.php?r=site/search',
	      		type:"GET",
	      		data:{
	      			keywords:$(this).val()
	      		},
	      		dataType: "json",
	      		beforeSend:function(){},     
	      		success: function(json) {
					if(json.hasOwnProperty("result") && json.result != 1){
						$(".search_result").css("display","none");
						return false;
					}
					
					if(json.hasOwnProperty("html")){
						$(".search_result").html(json.html);
				  		$(".search_result").css("display","block");
					}
					
	      		},
	      		error:function(msg){ console.log(msg);},
	      		complete:function(){}
	    	});
	 	}
    	
	})

	$('#search').focusout(function(){
		setTimeout(function(){ $(".search_result").css("display","none"); },800);
  	});
JS;


$this->registerJs($script);
?>