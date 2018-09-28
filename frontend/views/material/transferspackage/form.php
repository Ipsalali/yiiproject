<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\Currency;
use common\models\Seller;
use common\models\Client;

$sellers = Seller::getSellers();
$clients = Client::find()->All();

$this->title = isset($model->id)? "Перевод: ".$model->name : "Новый перевод";
$this->params['breadcrumbs'][] = ['label'=>"Список заявок",'url'=>Url::to(['transferspackage/index'])];
if(isset($model->id)){
    $this->params['breadcrumbs'][] = ['label'=>$model->name,'url'=>Url::to(['transferspackage/read','id'=>$model->id])];
}
$this->params['breadcrumbs'][]=$this->title;
?>


<div class="row">
	<div class="col-12">
		<?php $form = ActiveForm::begin(['id'=>'tranfer_form','options' => ['enctype' => 'multipart/form-data']]); ?>
        <div class="card">
            <div class="card-header card-header-primary">
                <div class="row">
                    <div class="col">
                        <h3 class="card-title"><?php echo Html::encode($this->title)?></h3>
                    </div>
                    <div class="col text-right">
                        <div class="btn-group">
                            <?php echo Html::submitButton('Сохранить', array('class'=>'btn btn-primary')); ?>
                            <?php echo isset($model->id) ? Html::a('Подробнее',['transferspackage/read','id'=>$model->id], ['class' => 'btn btn-success']) : null; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
					<div class="col-4">
					<?php echo $form->field($model,'name')->textInput(); ?>
					</div>

					<div class="col-2">
						<?php echo $form->field($model,'date')->widget(\yii\jui\DatePicker::classname(),['language' => 'ru','dateFormat'=>'dd-MM-yyyy',"options"=>array("class"=>"form-control",)]); ?>
					</div>
					
				</div>
				<div class="row">
					<?php 
						if(!isset($model->id)){
					?>
						<div class="col-2">
							<?php 
								echo $form->field($model,'status')->dropDownList(ArrayHelper::map($model->getStatuses(),'id','title'),['prompt'=>'Статус']);
							?>
						</div>
						<div class="col-2">
							<?php echo $form->field($model,'status_date')->widget(\yii\jui\DatePicker::classname(),['language' => 'ru','dateFormat'=>'dd-MM-yyyy',"options"=>array("class"=>"form-control")]); ?>
						</div>
					<?php }else{
					    echo Html::hiddenInput("package_id",$model->id);
					} ?>

					<div class="col-3">
						<?php echo $form->field($model,'files[]')->fileInput(['multiple' => true]);?>
					</div>

					<div class="col-3">
						<?php echo $form->field($model,'comment')->textarea();?>
					</div>
				</div>
		  
		        <div class="card-header card-header-tabs card-header-primary">
                    <div class="nav-tabs-navigation">
                        <div class="nav-tabs-wrapper">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item active"><a class='nav-link active' data-toggle="tab" href="#transfers">Услуги</a></li>
                                 <li class="nav-item"><a class='nav-link' data-toggle="tab" href="#expenses">Расходы</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
			    
                <div class="card-body">
                    
                
			    <div class="tab-content">
			        <div id="transfers" class="tab-pane  active">
        				
        				    <div class="card-header">
        				        <h3 class="card-title">Информация об услугах</h3>
        				        <div class="row">
        				            <div class='col-2'>
                					    <?php echo Html::a("Добавить услугу",['transferspackage/get-row-service'],['class'=>'btn btn-primary','id'=>'btnAddRowServise'])?>
        				            </div>
                				</div>
        				    </div>
        				
            				<div class="card-body">
                				<table id="services_table" class="table table-sm table-bordered table-collapsed table-hovered">
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
                								if(isset($transfers) && is_array($transfers) && count($transfers)){
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
                                                            		        echo Html::a("<i class=\"material-icons\">close</i>",['transferspackage/remove-transfer-ajax','id'=>$id],['class'=>'btn btn-danger btn-sm btn-round removeRowFromBd']);
                                                            		    else
                                                            		        echo Html::a("<i class=\"material-icons\">close</i>",null,['class'=>'btn btn-danger btn-sm btn-round removeRow']);
                                                            		
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
        				
    				
    				
    				<!-- Tab 2 -->
    				<div id="expenses" class="tab-pane fade in">
    				    
        				    <div class="card-header">
        				        <h3 class="card-title">Информация о расходах</h3>
        				        <div class="row">
        				            <div class='col-12'>
                					    <?php echo Html::a("Добавить расход",['transferspackage/get-row-expenses'],['class'=>'btn btn-primary','id'=>'btnAddRowExpenses'])?>
        				            </div>
                				</div>
        				    </div>
        				
            				<div class="card-body">
                					<table id="expenses_table" class="table table-sm table-bordered table-collapsed table-hovered">
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
                                                            		        echo Html::a("<i class=\"material-icons\">close</i>",['transferspackage/remove-expenses-ajax','id'=>$id],['class'=>'btn btn-danger btn-sm btn-round removeRowFromBd']);
                                                            		    else
                                                            		        echo Html::a("<i class=\"material-icons\">close</i>",null,['class'=>'btn btn-danger btn-sm btn-round removeRow']);
                                                            		
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
		<?php ActiveForm::end();?>
	</div>
</div>

<?php


$script = <<<JS
	
	


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
				type:"GET",
				dataType:"json",
				beforeSend:function(){
					send_remove = 1;
				},
				error:function(msg){
					console.log(msg);
				},
				success:function(json){
					if(json.hasOwnProperty("result") && json.result && row.length){
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
