<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\Currency;

$this->title = isset($model->id)? "Перевод: ".$model->name : "Новый перевод";
?>
<div class="row">
	<div class="col-xs-12">

		<?php $form = ActiveForm::begin(['id'=>'tranfer_form','options' => ['enctype' => 'multipart/form-data']]); ?>
		<div class="row">
			<div class="col-xs-4">
				<h3>
					<?php echo Html::encode($this->title)?>
				</h3>
			</div>
			<div class="col-xs-8">
				<div class="pull-right btn-group" style="margin-top: 20px;">
				    <?php echo Html::submitButton('Сохранить', array('class' => 'btn btn-primary')); ?>
				    <?php echo isset($model->id) ? Html::a('Подробнее',['transferspackage/read','id'=>$model->id], array('class' => 'btn btn-success')) : null?>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
				<div class="row">
					<div class="col-xs-4">
					<?php echo $form->field($model,'name')->textInput(); ?>
					</div>

					<div class="col-xs-2">
						<?php echo $form->field($model,'date')->widget(\yii\jui\DatePicker::classname(),['language' => 'ru','dateFormat'=>'dd-MM-yyyy',"options"=>array("class"=>"form-control",)]); ?>
					</div>
					
				</div>
				<div class="row">
					<?php 
						if(!isset($model->id)){
					?>
						<div class="col-xs-1">
							<?php 
								echo $form->field($model,'status')->dropDownList(ArrayHelper::map($model->getStatuses(),'id','title'),['prompt'=>'Статус']);
							?>
						</div>
						<div class="col-xs-1">
							<?php echo $form->field($model,'status_date')->widget(\yii\jui\DatePicker::classname(),['language' => 'ru','dateFormat'=>'dd-MM-yyyy',"options"=>array("class"=>"form-control")]); ?>
						</div>
					<?php }else{
					    echo Html::hiddenInput("package_id",$model->id);
					    //echo $form->field($model,'id')->hiddenInput()->label(false);
					} ?>

					<div class="col-xs-3">
						<?php echo $form->field($model,'files[]')->fileInput(['multiple' => true]);?>
					</div>

					<div class="col-xs-3">
						<?php echo $form->field($model,'comment')->textarea();?>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12">
			    
			    <ul class="nav nav-tabs">
		  			<li class="active"><a data-toggle="tab" href="#transfers">Наименования</a></li>
		  			<li><a data-toggle="tab" href="#expenses">Расходы</a></li>
				</ul>
			    
			    <div class="tab-content">
			        <div id="transfers" class="tab-pane fade in active">
        				
        				<div class="panel panel-default">
        				    <div class="panel-heading">
        				        Информация об услугах
        				        <div class="row">
        				            <div class='col-xs-12'>
                					    <?php echo Html::a("Добавить услугу",['transferspackage/get-row-service'],['class'=>'btn btn-primary','id'=>'btnAddRowServise'])?>
        				            </div>
                				</div>
        				    </div>
        				
            				<div class="panel-body">
            				    <div id="services_table_block">
                					<table id="services_table" class="table table-bordered table-collapsed table-hovered">
                						<thead>
                							<th>#</th>
                							<th>Клиент</th>
                							<th>Наименование</th>
                                            <th>Валюта</th>
                                            <th>Курс</th>
                							<th>Сумма</th>
                							<th>Сумма руб</th>
                							<th>Комментарий</th>
                							<th></th>
                						</thead>
                						<tbody>
                							<?php
                								if(isset($transfers) && is_array($transfers) &&count($transfers)){
                									foreach ($transfers as $k => $t) {
                									    
                									    $id = isset($t['id']) && $t['id'] ? (int)$t['id'] : null;
                									    $class = "Transfer[{$k}]";
                									    
                									    $errors = is_object($t) ? $t->getErrors() : array(); 
                									    
                										?>
                										   <tr class="<?php echo $id ?  "bdrow" :"" ?>">
                										       <td>
                										           <?php echo $k+1;?>
                										           <?php 
                										               echo $id ?  Html::hiddenInput($class."[id]",$id) : "";
                										           ?>
                										       </td>
                										       <td>
                                                            		<?php 
                                                            		    $e = array_key_exists('client_id',$errors) ? $errors['client_id'] : null;
                                                            		    echo Html::dropDownList($class."[client_id]",$t['client_id'] ? $t['client_id'] : null,ArrayHelper::map($clients,'id','name'),['prompt'=>'Выберите клиента','class'=>'form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>
                                                            	<td>
                                                            		<?php 
                                                            		    $e = array_key_exists('name',$errors) ? $errors['name'] : null;
                                                            		    echo Html::textInput($class."[name]",$t['name'],['class'=>'form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>


                                                                <td>
                                                                    <?php 
                                                                        $e = array_key_exists('currency',$errors) ? $errors['currency'] : null;
                                                                        echo Html::dropDownList($class."[currency]",$t['currency'],ArrayHelper::map(Currency::getCurrencies(),'id','title'),['prompt'=>'Выберите валюту','class'=>'form-control']);
                                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                                    ?>
                                                                </td>


                                                                <td>
                                                                    <?php 
                                                                        $e = array_key_exists('course',$errors) ? $errors['course'] : null;
                                                                        echo Html::textInput($class."[course]",$t['course'],['class'=>'form-control compute_sum']);
                                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                                    ?>
                                                                </td>

                                                            	<td>
                                                            		<?php 
                                                            		    $e = array_key_exists('sum',$errors) ? $errors['sum'] : null;
                                                            		    echo Html::textInput($class."[sum]",$t['sum'],['class'=>'sum form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>
                                                            	<td>
                                                            		<?php 
                                                            		    $e = array_key_exists('sum_ru',$errors) ? $errors['sum_ru'] : null;
                                                            		    echo Html::textInput($class."[sum_ru]",$t['sum_ru'],['class'=>'sum_ru form-control','readonly'=>1]);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>
                                                            	<td>
                                                            		<?php 
                                                            		    $e = array_key_exists('comment',$errors) ? $errors['comment'] : null;
                                                            		    echo Html::textInput($class."[comment]",$t['comment'],['class'=>'form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>
                                                            	<td>
                                                            		<?php 
                                                            		    
                                                            		    if($id)
                                                            		        echo Html::a("X",['transferspackage/remove-transfer-ajax','id'=>$id],['class'=>'btn btn-danger removeRowFromBd']);
                                                            		    else
                                                            		        echo Html::a("X",null,['class'=>'btn btn-danger removeRow','data-confirm'=>'Подтвердите свои дейсвтия']);
                                                            		
                                                            		?>
                                                            	</td>
                										   </tr>
                										<?php
                									}
                								}
                							?>
                						</tbody>
                					</table>
                				</div>
            				</div>
        				</div>
    				</div>
    				
    				<!-- Tab 2 -->
    				<div id="expenses" class="tab-pane fade in">
    				    
    				    <div class="panel panel-default">
        				    <div class="panel-heading">
        				        Информация о расходах
        				        <div class="row">
        				            <div class='col-xs-12'>
                					    <?php echo Html::a("Добавить расход",['transferspackage/get-row-expenses'],['class'=>'btn btn-primary','id'=>'btnAddRowExpenses'])?>
        				            </div>
                				</div>
        				    </div>
        				
            				<div class="panel-body">
            				    <div id="expenses_table_block">
                					<table id="expenses_table" class="table table-bordered table-collapsed table-hovered">
                						<thead>
                							<th>#</th>
                							<th>Дата</th>
                							<th>Поставщик</th>
                                            <th>Валюта</th>
                                            <th>Курс</th>
                							<th>Сумма</th>
                                            <th>Сумма Руб</th>
                							<th>Комментарий</th>
                							<th></th>
                						</thead>
                						<tbody>
                							<?php
                								if(isset($expenses) && is_array($expenses) &&count($expenses)){
                									foreach ($expenses as $k => $ex) {
                									    
                									    $id = isset($ex['id']) && $ex['id'] ? (int)$ex['id'] : null;
                									    $class = "SellerExpenses[{$k}]";
                									    
                									    $errors = is_object($ex) ? $ex->getErrors() : array(); 
                									    
                										?>
                										   <tr class="<?php echo $id ?  "bdrow" :"" ?>">
                										       <td>
                										           <?php echo $k+1;?>
                										           <?php 
                										               echo $id ?  Html::hiddenInput($class."[id]",$id) : "";
                										           ?>
                										       </td>
                                                               <td>
                                                            		<?php 
                                                            		    $e = array_key_exists('date',$errors) ? $errors['date'] : null;
                                                            		    echo Html::input("date",$class."[date]",$ex['date'] ? date("Y-m-d",strtotime($ex['date'])) : null,['class'=>'form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                               </td>
                										       <td>
                                                            		<?php 
                                                            		    $e = array_key_exists('seller_id',$errors) ? $errors['seller_id'] : null;
                                                            		    echo Html::dropDownList($class."[seller_id]",$ex['seller_id'] ? $ex['seller_id'] : null,ArrayHelper::map($sellers,'id','name'),['prompt'=>'Выберите поставщика','class'=>'form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>
                                                                

                                                                <td>
                                                                    <?php 
                                                                        $e = array_key_exists('currency',$errors) ? $errors['currency'] : null;
                                                                        echo Html::dropDownList($class."[currency]",$ex['currency'],ArrayHelper::map(Currency::getCurrencies(),'id','title'),['prompt'=>'Выберите валюту','class'=>'form-control']);
                                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php 
                                                                        $e = array_key_exists('course',$errors) ? $errors['course'] : null;
                                                                        echo Html::textInput($class."[course]",$ex['course'],['class'=>'form-control compute_sum']);
                                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                                    ?>
                                                                </td>
                                                            	<td>
                                                            		<?php 
                                                            		    $e = array_key_exists('sum',$errors) ? $errors['sum'] : null;
                                                            		    echo Html::textInput($class."[sum]",$ex['sum'],['class'=>'sum form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>
                                                                <td>
                                                                    <?php 
                                                                        $e = array_key_exists('sum_ru',$errors) ? $errors['sum_ru'] : null;
                                                                        echo Html::textInput($class."[sum_ru]",$ex['sum_ru'],['class'=>'sum_ru form-control','readonly'=>1]);
                                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                                    ?>
                                                                </td>
                                                            	<td>
                                                            		<?php 
                                                            		    $e = array_key_exists('comment',$errors) ? $errors['comment'] : null;
                                                            		    echo Html::textInput($class."[comment]",$ex['comment'],['class'=>'form-control']);
                                                            		    echo is_array($e) && count($e) ? $e[0] : null;
                                                            		?>
                                                            	</td>
                                                            	<td>
                                                            		<?php 
                                                            		    
                                                            		    if($id)
                                                            		        echo Html::a("X",['transferspackage/remove-expenses-ajax','id'=>$id],['class'=>'btn btn-danger removeRowFromBd']);
                                                            		    else
                                                            		        echo Html::a("X",null,['class'=>'btn btn-danger removeRow','data-confirm'=>'Подтвердите свои дейсвтия']);
                                                            		
                                                            		?>
                                                            	</td>
                										   </tr>
                										<?php
                									}
                								}
                							?>
                						</tbody>
                					</table>
                				</div>
            				</div>
        				</div>
    				</div>
				</div>
			</div>
		</div>

		<?php ActiveForm::end();?>
	</div>
</div>

<?php


$script = <<<JS
	
	//Смена валюты
	// $("#transferspackage-currency").change(function(){
	// 	if($(this).val()){
	// 		var op = $(this).find("option[value="+$(this).val()+"]");
	// 		if(op.length){
	// 			$("#th_sum_span").html(op.html());
	// 		}else{
	// 			$("#th_sum_span").html("");
	// 		}
	// 	}
	// });


	var send_getRowService = 0;
	$("body").on("click","#btnAddRowServise",function(event){
		event.preventDefault();
		var n = $("#services_table tbody tr").length ? $("#services_table tbody tr").length : 0; 
		var action = $(this).attr("href");
		if(action && !send_getRowService){
			$.ajax({
				url:action,
				type:"GET",
				data:"n="+n,
				dataType:"json",
				beforeSend:function(){
					send_getRowService = 1;
				},
				error:function(msg){
					console.log(msg);
				},
				success:function(json){
					if(json.hasOwnProperty("html")){
						$("#services_table tbody").append(json.html);
					}
				},
				complete:function(){
					send_getRowService = 0;
				}
			});
		}
	});
	
	
	var send_getRowExpenses = 0;
	$("body").on("click","#btnAddRowExpenses",function(event){
		event.preventDefault();
		var n = $("#expenses_table tbody tr").length ? $("#expenses_table tbody tr").length : 0; 
		var action = $(this).attr("href");
		if(action && !send_getRowService){
			$.ajax({
				url:action,
				type:"GET",
				data:"n="+n,
				dataType:"json",
				beforeSend:function(){
					send_getRowExpenses = 1;
				},
				error:function(msg){
					console.log(msg);
				},
				success:function(json){
					if(json.hasOwnProperty("html")){
						$("#expenses_table tbody").append(json.html);
					}
				},
				complete:function(){
					send_getRowExpenses = 0;
				}
			});
		}
	});
    
    
    var send_remove = 0;
    $("body").on("click",".removeRowFromBd",function(event){
		
		event.preventDefault();
		
		if(!confirm("Подтвердите свои дейсвтия")) return;
		
		var action = $(this).attr("href");
		var row = $(this).parents("tr.bdrow"); 
		
		if(action && !send_remove){
			$.ajax({
				url:action,
				type:"POST",
				dataType:"json",
				beforeSend:function(){
					send_remove = 1;
				},
				error:function(msg){
					console.log(msg);
				},
				success:function(json){
					if(json.hasOwnProperty("result") && parseInt(json.result) && row.length){
						row.remove();
					}
				},
				complete:function(){
					send_remove = 0;
				}
			});
		}
	});

	$("body").on("click",".removeRow",function(event){
		$(this).parents("tr").remove();
	});
	
	
	var calcSumRu = function(course,sum){
	      if(!course || !sum || course == "NaN" || sum == "NaN") return;
	      
	      return parseFloat(course * sum).toFixed(2);
	};
	
	
	//Расчет суммы руб при изменении курса
	$("body").on("keyup","input.compute_sum",function(){
	    
	    var course = parseFloat($(this).val()).toFixed(2);
	    var tRow = $(this).parents("tr");  
	    if(tRow.length){
	       var s = $(tRow).find("input.sum");
	       var sru = $(tRow).find("input.sum_ru");
	       if(s.length && sru.length){
	           var sum = parseFloat(s.val()).toFixed(2);
	           sru.val(calcSumRu(course,sum));
	       }
	        
	    }
	});
	
	$("body").on("keyup","input.sum",function(){
	    
	    var sum = parseFloat($(this).val()).toFixed(2);
        var tRow = $(this).parents("tr");

        if(tRow.length){
            var course = parseFloat(tRow.find("input.compute_sum").val()).toFixed(2);
            var sum_ru_input = tRow.find("input.sum_ru");
            if(sum_ru_input.length && sum && course){
                sum_ru_input.val(calcSumRu(course,sum));
            }
        }
	      
	});

JS;

$this->registerJs($script);
?>
