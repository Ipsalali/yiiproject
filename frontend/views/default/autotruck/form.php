<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\helpers\Arrayhelper;
use yii\widgets\Pjax;
use common\models\Status;
use common\models\User;
use common\models\SupplierCountry;
use common\models\Sender;
use common\models\TypePackaging;
use common\models\Client;


$roleexpenses = 'autotruck/addexpenses';
$user = \Yii::$app->user->identity;
$userIsClientExtended = \Yii::$app->user->can("clientextended");
$canReadAutotruck = Yii::$app->user->can("autotruck/read");
$canReadColumRate = Yii::$app->user->can("read/app/rate");
$canReadColumSumUs = Yii::$app->user->can("read/app/sum_us");
$canReadColumSumRu = Yii::$app->user->can("read/app/sum_ru");

$list_status = Status::find()->orderBy(['sort'=>SORT_ASC])->all();
$countries = $userIsClientExtended ? SupplierCountry::find()->all() : $user->countries;

$packages = TypePackaging::find()->all();
$senders = Sender::find()->orderBy(['name'=>'DESC'])->all();
$clients = ($userIsClientExtended) ? [$user->client] : Client::find()->where(['isDeleted'=>0])->orderBy(['name'=>'DESC'])->all();

$expManagers = User::getSellers();

$newModel = !isset($autotruck->id);

$this->title = $newModel ? "Новая заявка" : "Заявка:".$autotruck->name;
?>

<div class="base_content">
	<div class="row">
		<div class="col-xs-12">
			<h1><?php echo $this->title ?></h1>
		</div>
	</div>

	<div class="row">
	<?php if($autotruck){?>
		<div class="col-xs-12">
			<div id="autotruck_tab_<?php echo $autotruck->id?>" class="autotruck_block">
				  <div class="panel panel-primary">
				  	<div class="panel-heading">
				  		<?php echo $this->title;?>
				  	</div>

				  	<?php $form = ActiveForm::begin(['id'=>'autotruck_and_app_update','options' => ['enctype' => 'multipart/form-data'],'method'=>"post"])?>
				  	<div class="row">
				  		<div class="form-actions">
				  			<?php echo Html::submitButton('Сохранить заявку',['id'=>'submit_update','class' => 'btn btn-primary pull-right', 'name' => 'autotruck-update-button']); ?>
				  			<?php 
				  				if($canReadAutotruck && $autotruck->id){
				  					echo Html::a('Отменить редактирование', array('autotruck/read','id' => $autotruck->id), array('class' => 'btn btn-error pull-right'));
				  				}

				  				if($autotruck->id){
				  					echo Html::hiddenInput("autotruck_id",$autotruck->id);
				  					if($autotruck->enabledPostVersionId){
				  						if($autotruck->postVersionId){
				  							echo $form->field($autotruck,'postVersionId')->hiddenInput(['value'=>$autotruck->postVersionId])->label(false);
				  						}elseif($autotruck->version_id){
				  							echo $form->field($autotruck,'postVersionId')->hiddenInput(['value'=>$autotruck->version_id])->label(false);
				  						}
				  						
				  					}
				  				}
				  			?>
    					</div>
    				</div>
    				<div class="autotruck_data">
    					<div class="row">
    						<div class="col-xs-2">
    							<?php echo $form->field($autotruck,'invoice')->textInput()?>
    						</div>
    						<div class="col-xs-2">
    							<?php echo $form->field($autotruck,'name')->textInput()?>
    						</div>
    						<div class="col-xs-2">
    							<?php echo $form->field($autotruck,'date')->widget(yii\jui\DatePicker::className(),['language'=>'ru','dateFormat'=>'dd-MM-yyyy','options'=>array("class"=>"form-control")])?>
    						</div>
    						<div class="col-xs-1">
								<?php echo $form->field($autotruck,'course')->textInput(array('class'=>'form-control compute_sum compute_course')); ?>
							</div>
							<div  class="col-xs-2">
								<?php echo $form->field($autotruck,'country')->dropDownList(ArrayHelper::map($countries,'id','country'),['prompt'=>'Выберите страну']);?>
							</div>
							<div class="col-xs-3">
								<?php echo $form->field($autotruck,'auto_number')->textInput()?>
							</div>
						</div>
						<div class="row">
							<div class="col-xs-3">
    							<div class="status">
    								<div style="float:left;">
										<?php 
											echo $form->field($autotruck,'status',['inputOptions'=>['name'=>'Autotruck[status]',"data-current"=>$autotruck->status,"class"=>"change_status form-control"]])->dropDownList(ArrayHelper::map($list_status,'id','title'),['prompt'=>'Выберите статус']);
										?>
									</div>
									<div class="date_status_block" >
										<?php 
											$activeStatusTrace = $autotruck->activeStatusTrace;
											$trace_date = $activeStatusTrace && isset($activeStatusTrace->trace_date) ? $activeStatusTrace->trace_date : date('Y-m-d');
										?>
										<label for="date_status" data-current="<?php echo $trace_date?>"></label>
										<input type="text" id="date_status" name="Autotruck[date_status]"  value="<?php echo $trace_date?>">
										
									</div>
									<div class="clear"></div>
									<label class="change_status_info"></label>
								</div>
								<ul>
									<?php
										
										$story = $autotruck->traceStory;
										if(is_array($story)){
											foreach ($story as $key => $s) { 
												$active_s = ($s->status_id == $autotruck->status)? "active_status" :'';
												?>
												<li class="app_status <?php echo $active_s?>">
													<?php echo $s->status->title?>
													<span><?php echo date('d.m.Y',strtotime($s->trace_date))?></span>
												</li>
									<?php	}  } ?>
								</ul>
							</div>
    						<div class="col-xs-3">
    							<?php echo $form->field($autotruck,'description')->textarea()?>
    						</div>
    						<div class="col-xs-3">
								<?php echo $form->field($autotruck,'file[]')->fileInput(['multiple' => true]);?>

								<div>
									<p>Итого кол-во мест: <?php echo $autotruck->appCountPlace?></p>
									<?php
										if(is_array($packages)){
											foreach ($packages as $key => $package) {
												$count = $autotruck->getAppCountPlacePackage($package->id);

												if($count > 0){ ?>
													<p><?php echo $package->title?>: <?php echo $count; ?></p>
												<?php }
											}
										}
									?>
								</div>
							</div>
							<div class="col-xs-3">
									<?php echo $form->field($autotruck,'auto_name')->textInput()?>
									<?php echo $form->field($autotruck,'gtd')->textInput()?>
									<?php echo $form->field($autotruck,'decor')->textInput()?>
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
	    					<div class="panel panel-default">
		    					<div class="panel-heading">
		    							Информация о наименованиях
		    							<div class="row">
											<div class="col-xs-12 autotruck_btns">
												<a class="add_app_item btn btn-primary" href="<?php echo Url::to(['autotruck/get-row-app']);?>" id='add_app_item' data-type="0">Добавить наименование</a>
												<a class="add_app_item btn btn-primary" href="<?php echo Url::to(['autotruck/get-row-app']);?>" id="add_service_item"  data-type="1">Добавить услугу</a>
											</div>
										</div>
		    							<div class="clearfix"></div>
		    						</div>
									<div class="panel-body autotruck_info">
										<div class="table autotruck_apps">
											<table id="app_table" class="table table-striped table-hover table-bordered table_update">
											<tbody>
												<tr>
													<th style='width: 2%;'>№</th>
													<th class="app_client" style='width: 12%;'>Клиент</th>
													<th class="app_sender" style='width: 12%;'>Отправитель</th>
													
													<th class="app_info">Наименование</th>
													<th class="app_place">Кол-во мест</th>
													<th class="app_package">Упаковка</th>
													<th class="app_weigth">Вес (кг)</th>
													<th class="app_rate" style="width: 3%;">Ставка ($)</th>
													<th class="app_sumus">Сумма ($)</th>
													<th class="app_sumru">Сумма (руб)</th>
													<th class="app_comment">Комментарий</th>
													<th style="width: 25px;"></th>
												</tr>
										<?php 
										if(isset($apps) && is_array($apps) && count($apps)){
											foreach ($apps as $key => $app) {
												$id = isset($app['id']) && $app['id'] ? (int)$app['id'] : null;
												$type_class = !$app['type'] ? "type_app" :"type_service";
												$class = "App[{$key}]";
												$errors = is_object($app) ? $app->getErrors() : array();
										 ?>
												<tr class="app_row <?php echo $type_class;?>">
													<td>
														<?php echo $key+1?> 
														<?php echo $id ? Html::hiddenInput($class."[id]",$id) : "";?>
														<?php 

                											if(isset($app->postVersionId) && $app['postVersionId']){
                												echo Html::hiddenInput($class."[postVersionId]",$app['postVersionId']);
                											}elseif(isset($app['version_id'])){
                												echo Html::hiddenInput($class."[postVersionId]",$app['version_id']);
                											}
                										?>
														<?php echo Html::hiddenInput($class."[type]",$app['type']);?>
													</td>

													<td>
														<?php 
                                                            $e = array_key_exists('client',$errors) ? $errors['client'] : null;
                                                            echo Html::dropDownList($class."[client]",$app['client'] ? $app['client'] : null,ArrayHelper::map($clients,'id','name'),['prompt'=>'Выберите клиента','class'=>'form-control']);
                                                            echo is_array($e) && count($e) ? $e[0] : null;
                                                          ?>
													</td>

													<td> 
													<?php 
														if(!$app['type']){
															$e = array_key_exists('sender',$errors) ? $errors['sender'] : null;
															echo Html::dropDownList($class."[sender]",$app['sender'] ? $app['sender'] : null,ArrayHelper::map($senders,'id','name'),['prompt'=>'Выберите отправителя','class'=>'form-control']);
															echo is_array($e) && count($e) ? $e[0] : null;
														}
													?> 
													</td>

													<td>
													<?php 
														$e = array_key_exists('info',$errors) ? $errors['info'] : null;
														echo Html::textInput($class."[info]",$app['info'],['class'=>'form-control app_info']);
														echo is_array($e) && count($e) ? $e[0] : null;
													?>	
													</td>

													<td>
													<?php 
														if(!$app['type']){
															$e = array_key_exists('count_place',$errors) ? $errors['count_place'] : null;
															echo Html::textInput($class."[count_place]",$app['count_place'],['class'=>'form-control app_place']);
															echo is_array($e) && count($e) ? $e[0] : null;
														}
													?>
													</td>

													<td> 
														<?php 
															if(!$app['type']){
																$e = array_key_exists('package',$errors) ? $errors['package'] : null;
																echo Html::dropDownList($class."[package]",$app['package'] ? $app['package'] : null,ArrayHelper::map($packages,'id','title'),['prompt'=>'Выберите упаковку','class'=>'form-control']);
																echo is_array($e) && count($e) ? $e[0] : null;
															}
														?>
													</td>
													
													<td>
													<?php 
														if($app['type']){
															echo Html::hiddenInput($class."[weight]",1); 
														}else{
															$e = array_key_exists('package',$errors) ? $errors['package'] : null;
															echo Html::textInput($class."[weight]",$app['weight'],['class'=>'form-control compute_sum compute_weight']);
															echo is_array($e) && count($e) ? $e[0] : null;
														}
													?>	
													</td>

													<td class='rate_td'>
														<?php if($canReadColumRate){?>
															<?php 
																$e = array_key_exists('rate',$errors) ? $errors['rate'] : null;
																echo Html::textInput($class."[rate]",$app['rate'],['class'=>'form-control compute_sum compute_rate']);
																echo is_array($e) && count($e) ? $e[0] : null;
															?>	
														<?php } ?>
													</td>
													<td class="summa_usa">
														<?php if($canReadColumSumUs){?>
															<?php 
																$e = array_key_exists('summa_us',$errors) ? $errors['summa_us'] : null;
																echo Html::textInput($class."[summa_us]",$app['summa_us'],['class'=>'form-control summa_us']);
																echo is_array($e) && count($e) ? $e[0] : null;
															?>	
														<?php } ?>
													</td>
													<td class="<?php echo $canReadColumSumRu ? 'summa': '';?>">
														<?php if($canReadColumSumRu){?>

														<?php echo !$app['type'] ? round($app['weight']*$app['rate']*$autotruck->course,2) : round($app['rate']*$autotruck->course,2) ?> руб

														<?php } ?>
													</td>
													<td>
													<?php 
														$e = array_key_exists('comment',$errors) ? $errors['comment'] : null;
														echo Html::textInput($class."[comment]",$app['comment'],['class'=>'form-control']);
														echo is_array($e) && count($e) ? $e[0] : null;
													?>	
													</td>
													<td>
													<?php 
                                                        if($id){
                                                            echo Html::a("X",['autotruck/removeappajax','id'=>$id],['class'=>'btn btn-danger remove_exists_app','data-id'=>$id]);
                                                        }else{
                                                            echo Html::a("X",null,['class'=>'btn btn-danger remove_app','data-confirm'=>'Подтвердите свои дейсвтия']);
                                                        }  		
                                                     ?>
													</td>
												
												</tr>
										<?php }
										}
									?>
											</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- Расходы таб -->
							<div id="expenses" class="tab-pane fade in">
								<div class="exp_data">
									<h3>Информация о расходах</h3>
									<div class="row">
										<div class="col-xs-12 autotruck_btns">
											<a class="btn btn-primary" href="<?php echo Url::to(['autotruck/get-row-exp']);?>" id='add_expenses_item'>Добавить расход</a>
										</div>
									</div>
									<table id="exp_table" class="table table-striped table-hover table-bordered table-condensed">
										<tr>
											<th>№</th>
											<th>Дата</th>
											<th class="exp_manager_id">Менеджер</th>
											<th>Сумма ($)</th>
											<th>Комментарий</th>
											<th></th>
										</tr>
										<?php
										if(isset($expenses) && is_array($expenses) &&count($expenses)){
											foreach ($expenses as $key => $exp) {
												$id = isset($exp['id']) && $exp['id'] ? (int)$exp['id'] : null;
												$class = "ExpensesManager[{$key}]";
                								$errors = is_object($exp) ? $exp->getErrors() : array();
										 ?>
												<tr class="exp_row">
													<td>
														<?php echo $key+1?>
                										<?php echo $id ?  Html::hiddenInput($class."[id]",$id) : "";?>
                										<?php 
                											if(isset($exp->postVersionId) && $exp->postVersionId){
                												echo Html::hiddenInput($class."[postVersionId]",$exp['postVersionId']);
                											}elseif(isset($exp['version_id'])){
                												echo Html::hiddenInput($class."[postVersionId]",$exp['version_id']);
                											}
                										?>
													</td>
													<td>
                                                    <?php 
                                                        $e = array_key_exists('date',$errors) ? $errors['date'] : null;
                                                        echo Html::input("date",$class."[date]",$exp['date'] ? date("Y-m-d",strtotime($exp['date'])) : null,['class'=>'form-control']);
                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                    ?>
                                                    </td>
                                                    <td>
                                                    <?php 
                                                        $e = array_key_exists('manager_id',$errors) ? $errors['manager_id'] : null;
                                                        echo Html::dropDownList($class."[manager_id]",$exp['manager_id'] ? $exp['manager_id'] : null,ArrayHelper::map($expManagers,'id','name'),['prompt'=>'Выберите менеджера','class'=>'form-control manager_id']);
                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                    ?>
                                                    </td>
                                                    <td>
                                                    <?php 
                                                        $e = array_key_exists('cost',$errors) ? $errors['cost'] : null;
                                                        echo Html::textInput($class."[cost]",$exp['cost'],['class'=>'cost form-control']);
                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                    ?>
                                                    </td>
													<td>
                                                    <?php 
                                                        $e = array_key_exists('comment',$errors) ? $errors['comment'] : null;
                                                        echo Html::textInput($class."[comment]",$exp['comment'],['class'=>'form-control']);
                                                        echo is_array($e) && count($e) ? $e[0] : null;
                                                   	?>
                                                    </td>

													<td>
													<?php 
                                                        if($id)
                                                           	echo Html::a("X",['autotruck/removeexpajax','id'=>$id],['class'=>'btn btn-danger remove_exists_exp','data-id'=>$id]);
                                                        else
                                                            echo Html::a("X",null,['class'=>'btn btn-danger remove_exp','data-confirm'=>'Подтвердите свои дейсвтия']);
                                                            		
                                                        ?>
													</td>
												
												</tr>
										<?php }
									} ?>
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
</div>

<?php

$script = <<<JS
	var sendReqGetRowApp = 0;
	$(".add_app_item").click(function(event){
		
		event.preventDefault();
        var thisBtn = $(this);
		var table = $("#app_table");
		var row_count = table.find("tr.app_row").length;
		var app_type = parseInt($(this).data("type"));
		var href = $(this).attr("href");
		
		if(sendReqGetRowApp) return;
		
		$.ajax({
			url:href,
			data:{
				n:row_count,
				type:app_type
			},
			datetype:'json',
			beforeSend:function(){
			    sendReqGetRowApp = 1;
			    thisBtn.attr("disabled",true);
			},
			success:function(resp){
				if(resp.hasOwnProperty("html")){
					table.append(resp.html);
				}
			},
			error:function(msg){
			},
			complete:function(){
			    thisBtn.attr("disabled",false);
			    sendReqGetRowApp = 0;
			}
		});
		
	});
    
    var sendReqGetRowExp = 0;
	$("#add_expenses_item").click(function(event){
		event.preventDefault();
		if(sendReqGetRowExp) return;
		var thisBtn = $(this);
		var table = $("#exp_table");
		var exp_row_c = table.find("tr.exp_row").length;
		var href = $(this).attr("href");
		
		$.ajax({
			url:href,
			data:{
				n:exp_row_c,
			},
			datetype:'json',
			beforeSend:function(){
			    sendReqGetRowExp = 1;
			    thisBtn.attr("disabled",true);
			},
			success:function(resp){
				if(resp.hasOwnProperty("html")){
					table.append(resp.html);
				}
			},
			error:function(msg){
			},
			complete:function(){
				sendReqGetRowExp = 0;
				thisBtn.attr("disabled",false);
			}
		});
	});


		$("#app_table").on("click",'.remove_app',function(){
			$(this).parents('.app_row').remove();
		});

		$("#exp_table").on("click",'.remove_exp',function(){
			$(this).parents('.exp_row').remove();
		});


		$("#autotruck_and_app_update").submit(function(event){

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

			//Проверяем на заполненность менеджера расхода, если расходы доступны
			if($("select.manager_id").length){
				$("select.manager_id").each(function(e,i){
					if($(this).val() == '' || !$(this).val()){
						$(this).css('outline','1px solid #f00');
						$(this).attr('placeholder','Выберите менеджера!');
						valid = false;
					}else{
						$(this).css('outline','1px solid #0f0');
					}
				})
			}
			//Проверяем cost
			if($("input.cost").length){
				$("input.cost").each(function(e,i){
					if($(this).val() == '' || !$(this).val()){
						$(this).css('outline','1px solid #f00');
						$(this).attr('placeholder','Укажите сумму');
						valid = false;
					}else{
						$(this).css('outline','1px solid #0f0');
					}
				})
			}

			if(!valid)
				event.preventDefault();
			
			
		})


		//Удаление наименовании
		$("#app_table").on("click",".remove_exists_app",function(event){
			event.preventDefault();
			var id = parseInt($(this).data("id"));
			var r_rw = $(this).parents('.app_row');
			var href = $(this).attr("href");
			if(id  && window.confirm('Вы действительно хотите удалить выделенный объект?')){
				$.ajax({
					url:href,
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

		$("#exp_table").on("click",".remove_exists_exp",function(event){
			event.preventDefault();
			var id = parseInt($(this).data("id"));
			var r_rw = $(this).parents('.exp_row');
			var href = $(this).attr("href");
			if(id && window.confirm('Вы действительно хотите удалить выделенный объект?')){
				$.ajax({
					url:href,
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
JS;

$this->registerJs($script);
?>

<?php
	if(isset($autotruck->id) && $autotruck->id && Yii::$app->hasModule("websocket")){
		
		
		$t = $autotruck::tableName();
		$record_id = $autotruck->id;
		$user_id = Yii::$app->user->id;
		$redirectLocation = Url::to(['autotruck/read','id'=>$autotruck->id,'cause'=>403,'user_id'=>$user_id]);
		$remove_url = Url::to(['websocket/worker/remove-event']);
		$websocket = Yii::$app->getModule("websocket");

		echo  \WSUserActions\widgets\websocket\WebSocket::widget([
			'host'=>$websocket->websocket_localhost.":".$websocket->websocket_port,
			'table_name'=>$t,
			'record_id'=>$autotruck->id,
			'user_id'=>$user_id,
			'event'=>'update',
			'resetUrl'=>$remove_url,
			'redirectLocation'=>$redirectLocation
		]);
	}
?>
