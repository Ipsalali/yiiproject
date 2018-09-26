<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use common\models\ClientCategory;

$this->title = 'Рассылка';
?>
<div class="row">
        <div class="col-xs-6">
            <h2>Рассылка</h2>
        </div>
</div>

<?php if(Yii::$app->session->hasFlash('SpenderSaved')): ?>
<div class="alert alert-success">
    Письмо отправлено.
</div>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('SpenderError')): ?>
<div class="alert alert-danger">
    Письмо не удалось отправить.
</div>
<?php endif; ?>

<?php $form = ActiveForm::begin(['id' => 'spender_letter','action'=>Url::to(['spender/send'])]); ?>
<div class="row">
    
    <div class="col-xs-6">
    <?php echo $form->field($spender, 'theme')->textInput(); ?>
    
     <?php
        echo $form->field($spender, 'body')->widget(CKEditor::className(),[
            'editorOptions' => ElFinder::ckeditorOptions(['elfinder'],[
            'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
            'inline' => false, //по умолчанию false
               ]),
    ]);
    ?>
    </div>
    <div class="col-xs-6">
        <div class="row">
            <div class="col-xs-12">
                <div class="ignored_clients">
                    <label>Исключенные:</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-6 ignor_block">
                <input type="hidden" name="ignore_values" id="ignore_values">
                <label for="input_ignore_list" class="control-label">Введите имя клиента</label>
                <input type="text" name="input_ignore_list" id="input_ignore_list" class="form-control">
                <ul id="ignor_list">
                    
                </ul>
            </div>
        </div>
        <div class="row" style="margin-top: 15px;">
            <div class="col-xs-6 category_select">
                <label>Выберите категорию клиентов:</label>
                <?php echo Html::checkboxList("category",null,ArrayHelper::map(ClientCategory::find()->all(),'cc_id','cc_title'),[])?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 block_send-letter-button">
                <?php echo Html::submitButton('Отправить рассылку',['class' => 'btn btn-success send-letter-button', 'name' => 'send-letter-button','data-confirm'=>'Вы точно хотите отправить?']); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 spender_result">
                
            </div>
        </div>
        

    </div>
</div>
<?php ActiveForm::end(); ?>
<?php

$script = <<<JS
    $("#input_ignore_list").keyup(function(){
        var v = $(this).val();
        if(v.length > 2){
            $.ajax({
                url:"index.php?r=spender/get-client",
                type:"POST",
                data:"key="+v,
                dataType:"json",
                success:function(json){
                    console.log(json);
                    if(json.result && json.hasOwnProperty("client") && json.client.length>0){
                        var count_cl = json.client.length;
                        var html = "";
                        for(var i=0; i<count_cl; i++){
                            html +="<li class='client-item' data-id='"+json.client[i].id+"' data-name='"+json.client[i].name+"'>";
                                html += json.client[i].name;
                            html +="</li>";
                        }

                        $("#ignor_list").html(html);
                        if($("#ignor_list").length){
                            $("#ignor_list").show();
                        }
                    }
                }
            })
        }
    });

    $("#input_ignore_list").focus(function(){
        if($("#ignor_list li").length)
            $("#ignor_list").show();
    });
    

    $("#spender_letter").click(function(event){
        if(event.target != $("#input_ignore_list")[0]){
            $("#ignor_list").hide();
        }
    });

    var add_ignore_value = function(new_val){
        var values = $("#ignore_values").val();
        var arr_v = values.length? values.split(","):[];
        
        var id = parseInt(new_val.data("id")).toString();
        
        if(arr_v.indexOf(id) >= 0) return;
            
        arr_v.push(id);

        $("#ignore_values").val(arr_v.join(","));
        var span = $("<span/>").attr("id","ignore_"+id).addClass("ignored_item").attr("data-id",id).html(new_val.data("name"));
        
        span.append($("<span/>").addClass("remove_ignore").text(" x "));
        //span.append($("<span/>").text("; "))

        $(".ignored_clients").append(span);
    }

    var remove_ignore_value = function(val){
        var values = $("#ignore_values").val();
        var arr_v = values.length? values.split(","):[];
        console.log(val);
        var id = parseInt(val.data("id")).toString();
        console.log(arr_v);
        console.log(id);
        var index = arr_v.indexOf(id);
        if(index >= 0){
            arr_v.splice(index, 1);
            val.remove();
            $("#ignore_values").val(arr_v.join(","));
        }

        

    }

    $("body").on("click","#ignor_list li",function(event){
        add_ignore_value($(this));
    });


    $("body").on("click",".remove_ignore",function(event){
        var span = $(this).parent("span.ignored_item");        
        remove_ignore_value(span);
    });
    
    var sended = 0;
    $("#spender_letter").submit(function(event){
        
        event.preventDefault(); 
        
        var action =$(this).attr("action");
        var dataForm =$(this).serialize();

        if(!sended){
            
            $.ajax({
                url:action,
                type:"POST",
                data:dataForm,
                dataType:"json",
                beforeSend:function(){
                    sended = 1;
                    $(".load_block").show();
                    $(".send-letter-button").prop("disabled",true);
                },
                success:function(json){

                    if(parseInt(json.result)){
                        $(".spender_result").html($("<label/>").addClass("ok").text("Письмо отправлено!!!"));
                    }else{
                        
                        $(".spender_result").html($("<label/>").addClass("error").text("Письмо не удалось отправить. Обратитесь к администратору!!!"));
                    }
                },
                error:function(msg){
                    console.log(msg);
                    $(".spender_result").html($("<label/>").addClass("error").text("Возникла ошибка!!!"));
                },
                complete:function(){
                    $(".load_block").hide();
                    $(".send-letter-button").prop("disabled",false);
                    sended = 0;
                }
            })
        }
    })
JS;

$this->registerJS($script);
?>
<div class="load_block">
    <div class="load_gif"><img src='images/load_clock.gif'/></div>
</div>