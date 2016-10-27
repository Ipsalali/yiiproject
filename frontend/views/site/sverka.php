<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use yii\bootstrap\ActiveForm;
use common\models\User;
use frontend\models\PaymentsExpenses;
use frontend\models\ExpensesManager;
use yii\helpers\StringHelper;
$expensesPeople = User::getExpensesManagers();



?>

<div class="container">
	<div class="row">
		<div class="col-xs-4">
			<h2>Отчет "Сверка"</h2>
		</div>
	</div>
	<div class="row">
		<?php $form = ActiveForm::begin(['id'=>'sverka','action'=>["site/sverka"],'method'=>'GET'])?>
		<div class="col-xs-4">

			<?php
				$layout = <<< HTML
    				{input1}
    				{separator}
    				{input2}
HTML;
				echo DatePicker::widget([
							'name' => 'date_from',
							'name2' => 'date_to',
							'value'=> (!$data_params['date_from']) ? date("d.m.Y",time()) : $data_params['date_from'],
							'value2'=> (!$data_params['date_to']) ? date("d.m.Y",time()) : $data_params['date_to'],
							'language'=>'ru',
							'options' => ['placeholder' => 'Начальная дата'],
							'options2' => ['placeholder' => 'Конечная дата'],
							'type' => DatePicker::TYPE_RANGE,
							'separator'=>" по ",
							'layout'=>$layout,
							'pluginOptions'=>[
								'autoclose'=>true,
        						'format' => 'dd.mm.yyyy',
        						'todayHighlight' => true
    						]
						])
			?>
		</div>
		<div class="col-xs-4">
			<?php echo Html::dropDownList("manager",$manager->id,ArrayHelper::map($expensesPeople,'id','name'),['class'=>'form-control','prompt'=>'Выберите поставщика'])?>
		</div>
		<div class="col-xs-4">
			<input type="submit" class="btn btn-primary" value="Найти">
		</div>
		<?php Activeform::end(); ?>
	</div>
	<?php if(count($sverka)){?>
	<div class="row">
		<div class="col-xs-12">
			<h3>Расходы <?php echo $manager->name ?$manager->name:"Не указано имя (".$manager->username.")"?> от <?php echo date("d.m.y",strtotime($data_params['date_from']))?> по <?php echo date("d.m.y",strtotime($data_params['date_to']))?></h3>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-3">
			<button id="add_pay" class="btn btn-success">Добавить оплату</button>
		</div>
	</div>

	<div class="row">
		<div class="col-xs-12">
		<?php $form = ActiveForm::begin(['id'=>'addPaymentsManager','action'=>["site/addpaymentsmanager"]])?>
			
			<table class="table" id="table_pay">
				<tr>
					<th>#</th>
					<th>Дата</th>
					<th>Сумма $</th>
					<th>Расход/Оплата</th>
					<th>Комментарии</th>
					<th>Действие</th>
				</tr>
				<?php $com=0; foreach ($sverka as $key => $sv) { 

					$model = ($sv['type']) ? PaymentsExpenses::findOne($sv['id']) : ExpensesManager::findOne($sv['id']);
					?>
					<tr class="<?php echo ($sv['type'])? "pay_row":"exp_row"; ?>">
						<td><?php echo ++$key;?></td>
						<td>
							<?php echo ($model instanceof ExpensesManager) ? Html::a(date("d.m.Y",strtotime($sv['date'])),array("autotruck/read",'id'=>$model->autotruck_id),array("target"=>"_blank")) : date("d.m.Y",strtotime($sv['date']));?>
						</td>
						<td class='td_input_sum'><?php echo ($sv['type'])? "-".$sv['sum']:"+".$sv['sum']; ?> $</td>
						<td><?php echo $sv['type']?"Оплата":"Расход"?></td>
						<td class='td_input_comment'><?php echo $sv['comment']?></td>
						<td>
							<?php 
								$action = ($sv['type']) ? "site/removepayajax":"autotruck/removeexpajax";
							?>
							<a class="btn btn-primary sverka_update_btn" data-state="0" data-id="<?php echo $sv['id']?>" data-model="<?php echo StringHelper::basename(get_class($model));?>"><i class="glyphicon glyphicon-pencil"></i></a>
							<button type="submit" data-action="<?php echo $action?>" data-id="<?php echo $sv['id']?>" class="btn btn-danger remove_exists_payexp">X</button>

						</td>
					</tr>
				<?php  
						if(!$sv['type']){
							$com += $sv['sum']; 
						}else{
							$com -= $sv['sum']; 
						}
					} ?>
				<tr id="foot_row">
					<td colspan="2"><strong>Итого <small>(Все расходы и оплаты в системе)</small></strong></td>
					<td colspan="6"><strong><?php echo  $manager->getManagerSverka(); ?> $</strong></td>
				</tr>
			</table>
			<div class="row">
				<div class="col-xs-12" style="margin-bottom: 10px; text-align: right;">
					<button id="submit_addPaymentsManager" style="display: none;" class="btn btn-primary">Сохранить</button>
				</div>
			</div>
			<?php ActiveForm::end();?>
		</div>
		
		<!-- <div class="col-xs-6">
			<?php $form = ActiveForm::begin(['id'=>'addPaymentsManager','action'=>["site/addpaymentsmanager"]])?>

			<table class="table" id="table_pay" style="margin-top: 20px;">
				<tr>
					<th>#</th>
					<th style="widht:100px;">Сумма $</th>
					<th>Комментарии</th>
					<th>Удаление</th>
				</tr>
				<?php if(count($payments)){?>
					<?php $com_pay = 0; foreach($payments as $key=>$pay){
							$com_pay +=$pay->sum;
						?>
						<tr class="pay_row">
							<td><?=$key+1?> 
							<input type="hidden" name="PaymentsExpenses[<?=$key?>][id]" value="<?=$pay->id?>">
							<input type="hidden" name="PaymentsExpenses[<?=$key?>][date]" value="<?=$pay->date?>">
							<input type="hidden" name="PaymentsExpenses[<?=$key?>][manager_id]" value="<?=$manager->id?>">
							</td>

							<td><? echo $form->field($pay,'sum',['inputOptions'=>['name'=>'PaymentsExpenses['.$key.'][sum]']])->textInput(array('class'=>'form-control sum '))->label(false)?></td>

							<td><? echo $form->field($pay,'comment',['inputOptions'=>['name'=>'PaymentsExpenses['.$key.'][comment]']])->textInput()->label(false)?></td>

							<td>
								<a class='btn btn-danger remove_exists_pay' data-id="<?=$pay->id?>">X</a>
							</td>
						</tr>
					<?php } ?>
				<?php } ?>
			</table>
			<?php ActiveForm::end();?>
		</div> -->
		<!-- <div class="col-xs-6">
			<div class="row">
				<div class="col-xs-6 col-xs-offset-3">
					<p>Итого расход: <?php echo $com?$com:0;?></p>
					<p>Итого оплата: <?php echo ($com_pay)?$com_pay:0;?></p>
					<p>Итого разница: <?php echo (($com_pay - $com) > 0)? "+".(int)$com_pay - $com : $com_pay - $com;?></p>
				</div>
			</div>
		</div> -->
	</div>
	<?php }elseif($manager->id){ ?>
		<div class="row">
		<div class="col-xs-12">
			<h4>У <?php echo $manager->name ?$manager->name:"Не указано имя (".$manager->username.")"?>  нет расходов по периоду от <?php echo date("d.m.y",strtotime($data_params['date_from']))?> по <?php echo date("d.m.y",strtotime($data_params['date_to']))?></h4>
		</div>
	</div>
	<?php } ?>
</div>

<?php if(count($sverka) && $manager->id){?>
<script type="text/javascript">
	$(function(){

		var row_count = <?=count($sverka)?>;

		var generate_exp_row = function(n){
			var ntr = '<tr class="pay_row new_row">'+
			           '<td>-'+
			           '<input type="hidden" name="PaymentsExpenses['+n+'][manager_id]" value="<?php echo $manager->id?>">'+
			           '<input type="hidden" name="PaymentsExpenses['+n+'][date]" value="<?php echo $data_params['date_to']?>"> </td>'+
			           '<td><?php echo date('d.m.Y',strtotime($data_params['date_to']))?></td>';
			

			var ntd_info = "<td class='td_input_sum'><input type='text' name='PaymentsExpenses["+n+"][sum]' class=\'sum form-control\'></td><td>Оплата</td>";
			var ntd_comment = "<td class='td_input_comment'><input type='text' name='PaymentsExpenses["+n+"][comment]' class=\'pay_comment form-control\'></td>";

			ntr += ntd_info+ntd_comment
			ntr+="<td><a class='btn btn-danger remove_pay'>X</a></td>";

			ntr +='</tr>';

			return ntr;
		}


		//Добавление новой строки для оплаты
		$("#add_pay").click(function(event){
			event.preventDefault();
			row_count +=1;
			$("#table_pay #foot_row").before(generate_exp_row(row_count));
			$("#submit_addPaymentsManager").show();
		});


		//Удаление новой добавленной строки
		$("#table_pay").on("click",'.remove_pay',function(){
			$(this).parents('.new_row').remove();
			if(!$("tr.new_row").length && !$(".tr_form_actived").length){
				$("#submit_addPaymentsManager").hide();
			}
		});

		//Сохранение добавленных оплат
		$("#submit_addPaymentsManager").click(function(event){

			event.preventDefault();
			var form = $("#addPaymentsManager");
			var valid = true;
			if(!row_count){
				alert("Не добавлено ни одной оплаты");
				event.preventDefault();
			}

			//Проверяем cost
			if($("input.sum").length){
				$("input.sum").each(function(e,i){
					if($(this).val() == '' || !$(this).val()){
						$(this).css('outline','1px solid #f00');
						$(this).attr('placeholder','Укажите сумму');
						valid = false;
					}else{
						$(this).css('outline','1px solid #0f0');
					}
				})
			}
			if(!valid){
				return;
			}
			var formData = form.serialize();
			if(valid){


				$.ajax({
					url:"index.php?r=site%2Faddpaymentsmanager",
					type:"POST",
					data:formData,
					dateType:'json',
					beforeSend:function(){},
					success:function(json){
						console.log(json);
						if(json['result']){
							location = window.location.href;
						}else{
							alert("Оплата не добавлена");
							console.log(json['messages']);
						}
					},
					error:function(msg){ console.log(msg);},
					complete:function(){}
				})
			}
		});
		//Удаление наименовании
		$("#table_pay").on("click",".remove_exists_payexp",function(event){
			
			event.preventDefault();
			var id = parseInt($(this).data("id"));
			var action = $(this).data("action");
			var dataForm = "id="+id;
			var r_rw = $(this).parents('tr');

			if(id && action && window.confirm('Вы действительно хотите удалить выделенный объект?')){
				$.ajax({
					url:"index.php?r="+action,
					type:"POST",
					data: dataForm,
					datetype:'json',
					beforeSend:function(){
						console.log('before');
					},
					success:function(json){
						console.log(json);
						if(json['error']){
							alert(json['error']['text']);
						}else{
							r_rw.remove();
						}
					},
					error:function(msg){
						console.log(msg.StatusText);
						console.log(msg.responsive);
					},
					complete:function(){
						console.log('complete');
					}
				});
			}
		})

		//формирование формы редактирование для оплат и расходов 
		$("#table_pay").on("click",".sverka_update_btn",function(event){
			var $this = $(this);
			
			var model = $this.attr("data-model");
			var id =  $this.attr("data-id");
			var state =  parseInt($this.attr("data-state"));
			
			var td_input_sum = $(this).parent().siblings(".td_input_sum");
			var td_input_comment = $(this).parent().siblings(".td_input_comment");
			
			if(model && id && !state){
				$this.parents("tr").addClass("tr_form_actived");
				$this.attr("data-state",1);
				$this.attr("data-old_sum",td_input_sum.text());
				$this.attr("data-old_comment",td_input_comment.text());
				var input_sum = "<input type='text' name='"+model+"["+id+"][sum]' value='"+Math.abs(parseInt(td_input_sum.text()))+"' class='form-control'><input type='hidden' name='"+model+"["+id+"][id]' value='"+id+"'>";
				var input_com = "<input type='text' name='"+model+"["+id+"][comment]' value='"+td_input_comment.text()+"' class='form-control'>";
				td_input_sum.html(input_sum);
				td_input_comment.html(input_com);
				$this.find("i").removeClass("glyphicon-pencil");
				$this.find("i").addClass("glyphicon-resize-small");
				$("#submit_addPaymentsManager").show();
			}else{
				$this.parents("tr").removeClass("tr_form_actived");
				var old_sum = $this.attr("data-old_sum");
				var old_com = $this.attr("data-old_comment");
				$this.attr("data-state",0);
				td_input_sum.html(old_sum);
				td_input_comment.html(old_com);
				$this.find("i").addClass("glyphicon-pencil");
				$this.find("i").removeClass("glyphicon-resize-small");
				if(!$(".tr_form_actived").length && !$(".new_row").length){
					$("#submit_addPaymentsManager").hide();
				}
			}
		});
	})
</script>
<?php } ?>