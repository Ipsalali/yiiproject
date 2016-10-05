<?php 

use yii\helpers\Html; 
use yii\bootstrap\ActiveForm;
use yii\helpers\Arrayhelper;
use common\models\Client;
use common\models\Status;
use yii\widgets\Pjax;
use common\models\SupplierCountry;

?>


<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h1>Заявка: <?=$autotruck->name?></h1>
			 <div class="new_app">
				<?php echo Html::a('Добавить заявку', array('autotruck/create'), array('class' => 'btn btn-primary')); ?>
			 </div>
		</div>
	</div>
<!-- <div class="left_bar"> -->
	<?php //echo Html::a('Добавить заявку', array('autotruck/create'), array('class' => 'btn btn-primary')); ?>

	<?php if($listAutotruck && 00){?>
		<ul class="list-group app_links">
			<?php foreach ($listAutotruck as $key => $app){
					 $active=($app->id == $autotruck->id)? "active_link" : "";
				?>
				<li class="list-group-item <?=$active?>" data-id="<?=$app->id?>">
					<span class="badge"><?=count($app->getApps())?></span>
					<?php echo Html::a($app->id.' от '.date("d.m.Y",strtotime($app->date)), array('autotruck/update','id'=>$app->id), array('class' => 'app_link','id'=>"app_<?=$app->id?>")); ?>
				</li>
			<?php $active=''; } ?>
		</ul>
	<?php } ?>
<!-- </div> -->

<div class="autotruck_update">

	<?php if(Yii::$app->session->hasFlash('AutotruckSaved')): ?>
	<div class="alert alert-success">
    	Заявка сохранена!
	</div>
	<?php endif; ?>


	<?php if($autotruck){?>
		<div class="app_blocks">

				<div id="autotruck_tab_<?=$autotruck->id?>" class="autotruck_block">
				  	<div class="panel panel-primary">
				  		<div class="panel-heading">
				  			<?=$autotruck->name?> №<?=$autotruck->id?>
				  		</div>

				  		<?php $form = ActiveForm::begin(['id'=>'autotruck_and_app_update'])?>
				  		<div class="row">
				  			<div class="form-actions">
				  				<?php echo Html::a('Отменить редактирование', array('autotruck/read','id' => $autotruck->id), array('class' => 'btn btn-error pull-right')); ?>
        						<?php echo Html::submitButton('Сохранить заявку',['id'=>'submit_update','class' => 'btn btn-primary pull-right', 'name' => 'autotruck-update-button']); ?>
    						</div>
    						<div class="clear"></div>
    					</div>
    					<div class="autotruck_data">
    						<div class="row">
    							<div class="col-xs-3">
    								<?php echo $form->field($autotruck,'name')->textInput()?>
    							</div>
    							<div class="col-xs-2">
    								<?php echo $form->field($autotruck,'date')->widget(yii\jui\DatePicker::className(),['language'=>'ru','dateFormat'=>'dd-MM-yyyy','options'=>array("class"=>"form-control")])?>
    							</div>
    							<div class="col-xs-1">
									<?php echo $form->field($autotruck,'course')->textInput(array('class'=>'form-control compute_sum compute_course')); ?>
								</div>
								<div  class="col-xs-2">
									<?php echo $form->field($autotruck,'country')->dropDownList(ArrayHelper::map(SupplierCountry::find()->all(),'id','country'),['prompt'=>'Выберите страну']);?>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-3">
    							<div class="status">
    								<div style="float:left;">
										<?php 
											if($autotruck->status){
												$activeStatusSort = $autotruck->activeStatus->sort;
												if($activeStatusSort)
													$list_status = Status::find()->where("sort >= ".$activeStatusSort)->orderBy(['sort'=>SORT_ASC])->all();
												else $list_status = array();
											}else{$list_status = Status::find()->orderBy(['sort'=>SORT_ASC])->all();}
										
											echo $form->field($autotruck,'status',['inputOptions'=>['name'=>'Autotruck[status]',"data-current"=>$autotruck->status,"class"=>"change_status form-control"]])->dropDownList(ArrayHelper::map($list_status,'id','title'));
										?>
									</div>
									<div class="date_status_block" >
										<label for="date_status" data-current="<?=date('Y-m-d',strtotime($autotruck->activeStatusTrace->trace_date))?>"></label>
										<input type="text" id="date_status" name="Autotruck[date_status]"  value="<?=date('Y-m-d',strtotime($autotruck->activeStatusTrace->trace_date))?>">
									</div>
									<div class="clear"></div>
									<label class="change_status_info"></label>
								</div>

								<ul>
									<?
										$autotruck->activeStatus->title;
										$story = $autotruck->traceStory;
										if(is_array($story)){
											foreach ($story as $key => $s) { 
												$active_s = ($s->status_id == $autotruck->status)? "active_status" :'';
												?>
												<li class="app_status <?=$active_s?>">
													<?=$s->status->title?>
													<span><?=date('d.m.Y',strtotime($s->trace_date))?></span>
												</li>
									<?	}  } ?>
								</ul>
								</div>
    						<div class="col-xs-5">
    							<?php echo $form->field($autotruck,'description')->textarea()?>
    						</div>
    					</div>
				  		</div>
    					<div class="panel panel-default">
    						<div class="panel-heading">
    							Информация о наименованиях
    							<div class="row">
									<div class="col-xs-12 autotruck_btns">
										<button class="btn btn-primary" id='add_app_item'>Добавить наименование</button>
										<button class="btn btn-primary" id="add_service_item">Добавить услугу</button>
									</div>
								</div>
    							<div class="clearfix"></div>
    						</div>
						<div class="panel-body autotruck_info">
							
							
							
							<div class="table autotruck_apps">
								<table id="app_table" class="table table-striped table-hover table-bordered">
								<tbody>
									<tr>
										<th>№</th>
										<th class="app_client">Клиент</th>
										<th class="app_info">Информация</th>
										<th class="app_weigth">Вес (кг)</th>
										<th class="app_rate">Ставка ($)</th>
										<th>Сумма (руб)</th>
										<th>Сумма ($)</th>
										<th>Комментарий</th>
										<th>Удалить</th>
									</tr>
							<?php 
							foreach ($autotruck->getApps() as $key => $app) {
								$type_class = !$app->type ? "type_app" :"type_service"
							 ?>
									<tr class="app_row <?php echo $type_class;?>">
										<td><?=$key+1?> <input type="hidden" name="App[<?=$key?>][id]" value="<?=$app->id?>"><input type="hidden" name="App[<?=$key?>][type]" value="<?=$app->type?>"> </td>
										<td> 
										<?php echo $form->field($app,'client',['inputOptions'=>['name'=>'App['.$key.'][client]']])->dropDownList(ArrayHelper::map(Client::find()->all(),'id','name'),['prompt'=>'Выберите клиента'])->label(false)?>
										 
										</td>
										<td><? echo $form->field($app,'info',['inputOptions'=>['name'=>'App['.$key.'][info]']])->textInput()->label(false)?></td>
										<td><? echo $app->type ? '<input type="hidden" name="App['.$key.'][weight]" value="1">' : $form->field($app,'weight',['inputOptions'=>['name'=>'App['.$key.'][weight]']])->textInput(array('class'=>'form-control compute_sum compute_weight'))->label(false)?></td>
										<td><? echo $form->field($app,'rate',['inputOptions'=>['name'=>'App['.$key.'][rate]']])->textInput(array('class'=>'form-control compute_sum compute_rate'))->label(false)?></td>
										<td class="summa_usa"><?php echo !$app->type ?  $app->weight*$app->rate : $app->rate?> $</td>
										<td class="summa"><?php echo !$app->type ? $app->weight*$app->rate*$autotruck->course : $app->rate*$autotruck->course ?> руб</td>
										
										

										<td><? echo $form->field($app,'comment',['inputOptions'=>['name'=>'App['.$key.'][comment]']])->textInput()->label(false)?></td>

										<td>
											<a class='btn btn-danger remove_exists_app' data-id="<?=$app->id?>">X</a>
										</td>
									
									</tr>
							<?php } ?>
								</tbody>
								</table>
							</div>
						</div>
						</div>
						<?php  ActiveForm::end()?>
					</div>
				</div>

		</div>
	<?php } ?>

</div>
<div class="clearfix"></div>

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

		var	ntd_status = '<div class="form-group field-app-status gen"><select name="App['+n+'][status]" class=\'app_status form-control\'>';
			ntd_status +='<option value="">Выберите статус</option>';
			<?php foreach (Status::find()->all() as $key => $cl) { ?>

				ntd_status += '<option value="<?=$cl->id?>"><?=$cl->title?></option>';

			<?php } ?>
			ntd_status +='</select></div>';

			ntd_status = '<div class="date_status_block">'+
											'<label for="date_status_'+n+'"></label>'+
											'<input type="date" id="date_status_'+n+'" name="App['+n+'][date_status]" class="hasDatepicker">'+
										'</div>' + ntd_status;

			ntd_status = "<td class='status_td'>"+ntd_status+"</td>";

		var ntd_info = "<td><input type='text' name='App["+n+"][info]' class=\'app_info form-control\'></td>";
		var ntd_weight = (!type)? "<td><input type='text' name='App["+n+"][weight]' class=\'app_weight compute_sum compute_weight form-control\'></td>" : "<td><input type='hidden' name='App["+n+"][weight]' value='1'></td>";
		var ntd_rate = "<td><input type='text' name='App["+n+"][rate]' class=\'app_rate compute_sum compute_rate form-control\'></td>";
		//var ntd_course = "<td><input type='text' name='App["+n+"][course]' class=\'app_course form-control\'></td>";
		var ntd_comment = "<td><input type='text' name='App["+n+"][comment]' class=\'app_comment form-control\'></td>";

		ntr += ntd_client+ntd_info+ntd_weight+ntd_rate+"<td class='summa_usa'></td><td class='summa'></td>"+ntd_comment; //ntd_status
		ntr+="<td><a class='btn btn-danger remove_app'>X</a></td>";

		ntr +='</tr>';

		return ntr;

	}


	$(function(){

		var row_count = <?=count($autotruck->getApps())?>;

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
		
		$("#app_table").on("click",'.remove_app',function(){
			$(this).parents('.app_row').remove();
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


		$("#app_table").on("click",".remove_exists_app",function(){
			var id = parseInt($(this).data("id"));
			
			var r_rw = $(this).parents('.app_row');

			if(id && window.confirm('Вы действительно хотите удалить выделенный объект?')){
				$.ajax({
					url:"index.php?r=autotruck/removeappajax",
					type:"POST",
					data:'id='+id,
					datetype:'json',
					beforeSend:function(){
						console.log('before');
					},
					success:function(json){
						console.log('success');
						console.log(json);
						if(json['error']){
							alert(json['error']['text']);
						}else{
							console.log('Deleted');
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

	})
</script>
</div>