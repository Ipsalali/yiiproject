$(function(){

	$("body").on("click",".remove_check",function(event){
		if(!confirm("Подтвердите свои действия!"))
			event.preventDefault();
	})

	$("body").on("click",".remove_org",function(event){

		var oid = parseInt($(this).siblings("input[name=\'org_id\']").val());

		if($("#toActiveOrg"+oid).prop("checked")){
			alert("Организация является активной, поэтому её невозможно удалить!");
			return false;
		}
		if(!confirm("Подтвердите свои действия!"))
			event.preventDefault();
	})


	$(".colorPicker").minicolors({
		format:'rgb',
		opacity:true,
		theme: 'bootstrap'
	});
	


	$(".toActiveOrg").change(function(event){

		event.preventDefault();
		var form = $(this).parents("form");
		var fdata = form.serialize();
		this_r = $(this);
		$.ajax({
			url:'index.php?r=organisation/toactive',
			type:"POST",
			data:fdata,
			dataType:"json",
			beforeSend:function(){
				$(".toActiveOrg").disabled = true;
			},
			success:function(json){
				if(json['result']){
					$("input.toActiveOrg").prop("checked",false);
					this_r.prop("checked",true);
				}
				$(".active_change").text(json['text']);
				$(".active_change").show();
			},
			error:function(msg){
				console.log(msg);
			},
			complete:function(){
				$(".toActiveOrg").disabled = false;
			}
		})
	})
})