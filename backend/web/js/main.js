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
	

})