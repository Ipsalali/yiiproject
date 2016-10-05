<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Client;
use common\models\Status;
use yii\helpers\ArrayHelper;
use common\models\SupplierCountry;
use frontend\models\ExpensesManager;
use common\models\User;

$roleexpenses = 'autotruck/addexpenses';
$expensesManager = new ExpensesManager;
$AutotruckExpenses =ExpensesManager::getAutotruckExpenses($autotruck->id);

?>

<div class="container">
	<div class="row">
		
	</div>
<?php if(Yii::$app->session->hasFlash('AutotruckEmpty')): ?>
<div class="alert alert-error">
    There was an error deleting your post!
</div>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('AutotruckCreateError')): ?>
<div class="alert alert-success">
    Произошла ошибка при добавлений заявки.
</div>
<?php endif; ?>

<?php $form = ActiveForm::begin(['id'=>'autotruck_and_app']); ?>
	
	<div class="form-actions">
        <?php echo Html::submitButton('Сохранить заявку',['id'=>'submit_create','class' => 'btn btn-primary pull-right', 'name' => 'autotruck-create-button']); ?>
    </div>

	<div class="autotruck_data">
		<h3>Информация о заявке</h3>
		<div class="row">
			
			<div class="col-xs-3">
				<?php echo $form->field($autotruck,'name')->textInput(array()); ?>
			</div>

			<div class="col-xs-2">
				<?php echo $form->field($autotruck,'date')->widget(\yii\jui\DatePicker::classname(),['language' => 'ru','dateFormat'=>'dd-MM-yyyy',"options"=>array("class"=>"form-control")]); ?>
			</div>
			
			<div class="col-xs-1">
				<?php echo $form->field($autotruck,'course')->textInput(array('class'=>'form-control compute_sum compute_course')); ?>
			</div>
			<div class="col-xs-2">
				<?php echo $form->field($autotruck,'country')->dropDownList(ArrayHelper::map(SupplierCountry::find()->all(),'id','country'),['prompt'=>'Выберите страну']);?>
			</div>
		</div>
		<div class="row">	
			<div class="col-xs-3">
			<div class="status">
    			<div style="float:left;">
				<?php echo $form->field($autotruck,'status',['inputOptions'=>["class"=>"form-control"]])->dropDownList(ArrayHelper::map(Status::find()->orderBy(['sort'=>SORT_ASC])->all(),'id','title'),['prompt'=>'Выберите статус']);?>
				<?php 
					

				?>
				</div>
					<div class="date_status_block" >
						<label for="date_status" data-current="<?=date('Y-m-d',time())?>"></label>
						<input type="date" id="date_status" class="date_for_status_create" name="Autotruck[date_status]" class="hasDatepicker" value="<?=date('Y-m-d',time())?>" data-change="0">
					</div>
					<div class="clear"></div>
					<label class="change_status_info"></label>
				</div>
			</div>
			<div class="col-xs-5">
				<?php echo $form->field($autotruck,'description')->textarea();?>
			</div>
		</div>
	</div>
	<?php if(Yii::$app->user->can($roleexpenses)){?>
		<ul class="nav nav-tabs">
		  	<li class="active"><a data-toggle="tab" href="#apps">Наименования</a></li>
		  	<li><a data-toggle="tab" href="#expenses">Расходы</a></li>
		</ul>
	<?php } ?>
	<div class="tab-content">
		<div id="apps" class="tab-pane fade in active">
			<div class="apps_data">
				<h3>Информация о наименованиях</h3>
				<div class="row">
					<div class="col-xs-12 autotruck_btns">
						<button class="btn btn-primary" id='add_app_item'>Добавить наименование</button>
						<button class="btn btn-primary" id="add_service_item">Добавить услугу</button>
					</div>
				</div>
				<table id="app_table" class="table table-striped table-hover table-bordered table-condensed">
					<tr>
						<th>№</th>
						<th class="app_client">Клиент</th>
						<th class="app_info">Информация</th>
						<th class="app_weigth">Вес (кг)</th>
						<th class="app_rate">Ставка ($)</th>
						<th>Сумма ($)</th>
						<th>Сумма (руб)</th>
						<!-- <th>Статус</th> -->
						<th>Комментарий</th>
						<th>Удаление</th>
					</tr>
				</table>
			</div>
		</div>
		<div id="expenses" class="tab-pane fade in">
			<div class="exp_data">
				<h3>Информация о расходах</h3>
				<div class="row">
					<div class="col-xs-12 autotruck_btns">
						<button class="btn btn-primary" id='add_expenses_item'>Добавить расход</button>
					</div>
				</div>
				<table id="exp_table" class="table table-striped table-hover table-bordered table-condensed">
					<tr>
						<th>№</th>
						<th class="exp_manager_id">Менеджер</th>
						<th>Сумма ($)</th>
						<th>Комментарий</th>
						<th>Удаление</th>
					</tr>
				</table>
			</div>
		</div>
	</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
	
	var generate_app_row = function(n,type=0){
		var type_class = (!type)? "type_app":"type_service";
		var ntr = '<tr class="app_row '+type_class+'"><td>-<input type="hidden" name="App['+n+'][type]" value="'+type+'"></td>';
		var	ntd_client = '<td><select name="App['+n+'][client]" class=\'app_client form-control\'>';
			ntd_client +='<option value="">Выберите клиента</option>';
			<?php foreach (Client::find()->all() as $key => $cl) { ?>

				ntd_client += '<option value="<?=$cl->id?>"><?=$cl->name?></option>';

			<?php } ?>
			ntd_client +='</select></td>';

		// var	ntd_status = '<td><select name="App['+n+'][status]" class=\'app_status form-control\'>';
		// 	ntd_status +='<option value="">Выберите статус</option>';
		// 	<?php foreach (Status::find()->all() as $key => $cl) { ?>

		// 		ntd_status += '<option value="<?=$cl->id?>"><?=$cl->title?></option>';

		// 	<?php } ?>
		// 	ntd_status +='</select></td>';

		var ntd_info = "<td><input type='text' name='App["+n+"][info]' class=\'app_info form-control\'></td>";
		var ntd_weight = (!type)? "<td><input type='text' name='App["+n+"][weight]' class=\'app_weight compute_sum compute_weight form-control\'></td>" : "<td><input type='hidden' name='App["+n+"][weight]' value='1'></td>";
		var ntd_rate = "<td><input type='text' name='App["+n+"][rate]' class=\'app_rate compute_sum compute_rate form-control\'></td>";
		//var ntd_course = "<td><input type='text' name='App["+n+"][course]' class=\'app_course form-control\'></td>";
		var ntd_comment = "<td><input type='text' name='App["+n+"][comment]' class=\'app_comment form-control\'></td>";

		ntr += ntd_client+ntd_info+ntd_weight+ntd_rate+"<td class='summa_usa'></td><td class='summa'></td>"+ntd_comment;/*ntd_status+*/
		ntr+="<td><a class='btn btn-danger remove_app'>X</a></td>";

		ntr +='</tr>';

		return ntr;

	}

	var geberate_exp_row = function(n){
		var ntr = '<tr class="exp_row"><td>-</td>';
		var	ntd_client = '<td><select name="ExpensesManager['+n+'][manager_id]" class=\'manager_id form-control\'>';
			ntd_client +='<option value="">Выберите менеджера</option>';
			<?php foreach (User::getExpensesManagers() as $key => $cl) { ?>

				ntd_client += '<option value="<?=$cl->id?>"><?=$cl->name?></option>';

			<?php } ?>
			ntd_client +='</select></td>';

		var ntd_info = "<td><input type='text' name='ExpensesManager["+n+"][cost]' class=\'cost form-control\'></td>";
		var ntd_comment = "<td><input type='text' name='ExpensesManager["+n+"][comment]' class=\'exp_comment form-control\'></td>";

		ntr += ntd_client+ntd_info+ntd_comment
		ntr+="<td><a class='btn btn-danger remove_exp'>X</a></td>";

		ntr +='</tr>';

		return ntr;
	}


	$(function(){

		var row_count = 0;
		var exp_row_c = 0;

		$("#add_app_item").click(function(event){
			event.preventDefault();
			row_count +=1;
			$("#app_table").append(generate_app_row(row_count));
		});

		$("#add_service_item").click(function(event){
			event.preventDefault();
			row_count +=1;
			$("#app_table").append(generate_app_row(row_count,1));
		});

		$("#add_expenses_item").click(function(event){
			event.preventDefault();
			exp_row_c +=1;
			$("#exp_table").append(geberate_exp_row(exp_row_c));
		});

		
		
		$("#app_table").on("click",'.remove_app',function(){
			$(this).parents('.app_row').remove();
		});

		$("#exp_table").on("click",'.remove_exp',function(){
			$(this).parents('.exp_row').remove();
		});


		$("#autotruck_and_app").submit(function(event){

			var valid = true;

			$('input.app_info').each(function(e,i){
				
				if($(this).val() == ''){
					$(this).css('outline','1px solid #f00');
					$(this).attr('placeholder','Заполните имя!');

					valid = false;

				}else{
					$(this).css('outline','1px solid #0f0');
				}

			})

			if(!valid)
				event.preventDefault();
			
		})

	})
</script>
</div>