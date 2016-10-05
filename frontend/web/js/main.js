$(function(){
    // Modernizr.load({
    //     test: Modernizr.inputtypes.date,
    //     nope: "js/jquery-ui.min.js",
    //     callback: function() {
    //       $("input[type=\'date\']").datepicker();
    //     }
    //   });

    $("#date_status").datepicker();
	//App Tabs

	$(".app_tabs").click(function(event){
		var id = parseInt($(this).data("id"));
		$(".app_tabs.active_tab").not(this).removeClass("active_tab");
		$(this).addClass("active_tab");

		$(".app_block").not("#autotruck_tab_"+id).removeClass("active_block");
		$("#autotruck_tab_"+id).addClass("active_block");
	})

	$(".date_status_block input").click(function(){
		$(".date_status_block input::-webkit-calendar-picker-indicator").trigger("click");
	})


	$("body").on("click",".remove_check",function(event){
		if(!confirm("Подтвердите свои действия!"))
			event.preventDefault();
	})



	//Фильтр клиентов
	$("#form-client-filters select").change(function(){
		$("#form-client-filters").submit();
	})

	//Фильтр заявок
	$("#form-autotrucks-filter select").change(function(){
		$("#form-autotrucks-filter").submit();
	})


	//Запрет ввода букв для вычисляемых полей; разрешаем точку код 46
	$("body").on("keypress",".compute_sum",function(key) {
        if((key.charCode < 48 || key.charCode > 57) && key.charCode != 46) return false;
    });


	//Вычисляем сумму при изсенении веса и ставки наименования
    $("body").on("keyup",".compute_rate,.compute_weight",function(event){
    	var parent_row = $(this).parents(".app_row");
    	var course =  parseFloat($(".compute_course").val());
    	var rate =  parseFloat(parent_row.find(".compute_rate").val());
    	var weight =  parent_row.hasClass("type_service")? 1 :parseFloat(parent_row.find(".compute_weight").val());

    	var s_ru = course* rate * weight;
        var s_usa = rate * weight;
    	if(course >0 && rate >0 && weight > 0 && s_ru > 0 && s_usa>0){
    		parent_row.find(".summa").text(s_ru.toFixed(2)+" руб");
            parent_row.find(".summa_usa").text(s_usa.toFixed(2)+" $");
    	}else{
    		parent_row.find(".summa").text("");
            parent_row.find(".summa_usa").text("");
    	}
    })


    //Вычисляем сумму всех наименовании заявки после изменения курса
    $("body").on("keyup",".compute_course",function(event){

    	var course =  parseFloat($(this).val());
    	$(".app_row").each(function(e){
    		var parent_row = $(this);
    		var rate =  parseFloat(parent_row.find(".compute_rate").val());
    		var weight =  parent_row.hasClass("type_service")? 1 :parseFloat(parent_row.find(".compute_weight").val());

    		var s_ru = course* rate * weight;
            var s_usa = rate * weight;
    		if(course >0 && rate >0 && weight > 0 && s_ru > 0 && s_usa>0){
    			parent_row.find(".summa").text(s_ru.toFixed(2)+" руб");
                parent_row.find(".summa_usa").text(s_usa.toFixed(2)+" $");
    		}else{
    			parent_row.find(".summa").text("");
                parent_row.find(".summa_usa").text("");
    		}
    	});
    	
    })


    //Изменение статуса
    $(".change_status").change(function(){
        var current = $(this).data("current");
        var selected = $(this).find("option:selected").val();
        var new_time = $("#date_status").val();
        var current_time = $("#date_status").siblings("label").data("current");
        if(selected != current && new_time == current_time){
            $(".date_status_block").trigger("click");
            $(".change_status_info").text("Изменился статус, выберите дату!");
        }else{
            $(".change_status_info").text("");
        }
    })

    $("#date_status").change(function(){
        var current_time = $(this).siblings("label").data("current");
        var new_time = $(this).val();
        var current = $(".change_status").data("current");
        var selected = $(".change_status").val();
        if(new_time != current_time){
            $(".change_status_info").text("");
        }else if(selected != current){
            $(".change_status_info").text("Изменился статус, выберите дату!");
        }   
    })

    //Отправляем данные на сервер если только статус изменен и изменена дата
    $("#autotruck_and_app_update").submit(function(event){
        var current = $(".change_status").data("current");
        var selected = $(".change_status").val();
        var new_time = $("#date_status").val();
        var current_time = $("#date_status").siblings("label").data("current");
        
        
        if(selected != current && new_time == current_time){
            $(".change_status_info").text("Изменился статус, выберите дату!");
            event.preventDefault();
        }else{
            $(".change_status_info").text("");
        }

    })


    $(".date_for_status_create").change(function(){
        $(this).attr("data-change",1);
        $(".change_status_info").text("");
    })

    //Отправляем данные на сервер если только статус изменен и изменена дата
    $("#autotruck_and_app").submit(function(event){
        
        var changed = parseInt($("#date_status").attr("data-change"));

        if(!changed){
            $(".change_status_info").text("Выберите дату изменения статуса!");
            event.preventDefault();
        }else{
            $(".change_status_info").text("");
        }

    })
})