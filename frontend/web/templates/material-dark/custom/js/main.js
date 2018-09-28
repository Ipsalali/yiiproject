$(function(){

    $(".nav-item.dropdown > a").click(function(event){
        event.preventDefault();
        $(this).siblings(".nav-second-level").toggle("show");
    });

    $("body").on("click",".remove_check",function(event){
        if(!confirm("Подтвердите свои действия!"))
            event.preventDefault();
    });

})