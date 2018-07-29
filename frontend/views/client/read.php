<?php 
use yii\helpers\Html;
use frontend\models\Autotruck;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use frontend\models\CustomerPayment;
use common\models\PaymentState;
use common\models\TypePackaging;
use yii\bootstrap\Modal;

$this->title = "Клиент ".$client->name;

$sum_states = PaymentState::getSumStates();
$packages = TypePackaging::find()->all();
?>

<div class="client_page">
<div class="row client_page_head">
	<div class="pull-left">
		 <?php echo Html::a('Клиенты', array('client/index'), array('class' => '')); ?>
	</div>
	<div class="pull-right btn-group">
	<?php echo Html::a("Журнал редактирования клиента",['client/client-story','id'=>$client->id],['id'=>'btnClientStory','class'=>'btn btn-success'])?>
    <?php echo Html::a('Редактировать', array('client/update', 'id' => $client->id), array('class' => 'btn btn-primary')); ?>
    <?php echo Html::a('Удалить', array('client/delete', 'id' => $client->id), array('class' => 'btn btn-danger remove_check')); ?>
	</div>
</div>
	
	<div class="row">
		<div class="col-xs-12">
			<h2>
				<?php echo Html::encode($client->name); ?>&nbsp(<span><?php echo Html::encode($client->categoryTitle); ?></span>)
			</h2>
		</div>
	</div>
	<div class="row">
	 	<div class="col-xs-7">
	 		<div class="row">
				<div class="col-xs-6">
					<h4>Название</h4>
					<p>
						<?php echo Html::encode($client->full_name); ?>
					</p>
				</div>
			<div class="col-xs-3">
				<h4>Email</h4>
				<p><?php echo Html::encode($client->user->email ? $client->user->email :"E-mail не указан"); ?></p>
			</div>
			<div class="col-xs-3">
				<h4>Телефон</h4>
				<p><?php echo Html::encode(($client->phone)?$client->phone:"Телефон не указан"); ?></p>
			</div>
			
			</div>
		
			<div class="row">
				<div class="col-xs-4">
					<h4>Ответственный:</h4>
					<p><?php echo Html::encode(($client->managerUser)?$client->managerUser->name:"Не закреплен к менеджеру."); ?></p>
				</div>
				<div class="col-xs-4">
					<h4>Договор:</h4>
					<p><?php echo Html::encode($client->contract_number); ?></p>
				</div>
				<div class="col-xs-4">
					<h4>Оплата по безналу:</h4>
					<p><?php echo Html::encode($client->payment_clearing); ?> %</p>
				</div>
			</div>
			<div class="row">
				<div class="col-xs-8">
					<h4>Описание:</h4>
					<p><?php echo Html::encode(($client->description)?$client->description:"Нет описания"); ?></p>
				</div>
				<div class="col-xs-4">
					<h4>Организация:</h4>
					<p><?php echo Html::encode(($client->organisation_pay_id)?$client->organisation->org_name:"Не привязан."); ?></p>
				</div>
			</div>
		</div>
		<div class="col-xs-5">
				<h4>График</h4>
				<?php if(count($grafik)){?>
					<div id="placeholder" style="width:400px;height:300px;"></div>
					<div id="grafik_info"></div>
				<?php } ?>
		</div>
		</div>
		<div class="row">
			<div class="col-xs-3">
			    	
				<p>Задолженность: 
				    <?php 
				    	echo $client->user->getManagerSverka();
				    	//echo $client->getDebt() - $client->getSumStateSum();
			    ?> $</p>
			
			</div>
			
		</div>
		<div class="row">
				<div class="col-xs-12">
					<?php if($autotrucks){?>
					<div class="app_blocks">
					
						<?php  foreach ($autotrucks as $key => $a){
							if($autotruck = Autotruck::find()->where("id=".$key)->one()){
							}else continue;
						?>
						<?php 
							//Не используется больше, рассмотреть удаление этого функционала
							$CustomerPayment = CustomerPayment::getCustomerPayment($client->id,$autotruck->id);
							$paymentState = $CustomerPayment->id ? $CustomerPayment->paymentState : PaymentState::getDefaultState();
						?>
				<div id="autotruck_tab_<?=$key?>">
				  	<div class="panel panel-primary">
				  		<div class="panel-heading">
				  			<div class="row">
					  			<div class="col-xs-9">
					  				<h4><?php echo Html::a($autotruck->name." №".$autotruck->id,array('autotruck/read','id'=>$autotruck->id))?>
					  					&nbsp&nbsp&nbsp
					  					<?php echo html::a("Редактировать",array("autotruck/update","id"=>$autotruck->id))?>
					  				</h4>
					  			</div>
					  			<div class="col-xs-3" style="text-align:center">
					  				<span><?=date("d.m.Y",strtotime($autotruck->date))?></span><br>
					  				<?php echo Html::a("Скачать счет", array("client/check","client"=>$client->id,"autotruck"=>$autotruck->id),array("class"=>"check btn btn-success"))?>
					  			</div>
				  			</div>
				  			
				  			<?php //echo Html::a("Отправить уведомления", array("client/sendnotification","client"=>$client->id,"autotruck"=>$autotruck->id),array("class"=>"notification check"))?>

				  		</div>
						<div class="panel-body autotruck_info">
							
							<div class="row">
								<div class="col-xs-4">
									<p><strong>Курс:</strong> <span><?php echo $autotruck->course; ?> руб.</span></p>
									
									<p><strong>Страна поставки:</strong> <span><?php echo $autotruck->countryName; ?></span></p>
									
                                    <p><strong>Номер машины:</strong> <?php echo $autotruck->auto_number?></p>
									<p><strong>Описание:</strong> <?php echo Html::encode($autotruck->description); ?></p>
									
                                    <p><strong>ГТД:</strong> <?php echo $autotruck->gtd?></p>
                                
								</div>
								<div class="col-xs-3">
									<h4>Статус:</h4>
										<ul>
											<?
												$autotruck->activeStatus->title;
												$story = $autotruck->traceStory;
												if(is_array($story)){
													foreach ($story as $s) { 
														$active_s = ($s->status_id == $autotruck->status)? "active_status" :'';
																	?>
														<li class="app_status <?=$active_s?>">
															<?=$s->status->title?>
															<span><?=date('d.m.Y',strtotime($s->trace_date))?></span>
														</li>
													<? }
												}
											?>
										</ul>
										<div>
										<p>Итого кол-во мест: <?php echo $autotruck->getAppCountPlace($client->id)?></p>
										<?php
											if(is_array($packages)){
												foreach ($packages as $key => $package) {
													$count = $autotruck->getAppCountPlacePackage($package->id,$client->id);

													if($count > 0){
														?>
														<p><?php echo $package->title?>: <?php echo $count; ?></p>
														<?php
													}
												}
											}
										?>
								    	</div>
								</div>
								<?php 
									//РАссмотреть безопасное отключение функционала CustomerPayment
								?> 
								<div class="col-xs-5" style="display: none;">
									<div class="row">
										<div class="col-xs-12">
											<p>
												<strong>Статус оплаты</strong>: <span style="color:<?=$paymentState->color?>"><?php echo $paymentState->title; ?></span>
											</p>
										</div>
									</div>
									<?php $form = ActiveForm::begin(['id'=>"paymentStateFor".$autotruck->id,'class'=>'paymentStateForm','action'=>['client/autotruckpayment','id'=>$CustomerPayment->id]])?>
									<div class="row">
										<div class="col-xs-4">
											<?php 
												echo $form->field($CustomerPayment,"autotruck_id",array('attribute'=>['class'=>"hid"]))->hiddenInput(['value' => $autotruck->id,'class'=>"hid"])->label(false);
												echo $form->field($CustomerPayment,"client_id")->hiddenInput(['value' => $client->id,'class'=>"hid"])->label(false);
											?>
											<?php 
												$options = [$paymentState->id=>['selected'=>true]];
												
												foreach ($sum_states as $key => $ss) {
													$options[$ss->id]['data-sum'] = 1;
												}

												echo $form->field($CustomerPayment,'payment_state_id',['inputOptions'=>["class"=>"form-control payment-state-select"]])->label(false)->dropDownList(ArrayHelper::map(PaymentState::find()->orderBy(['id'=>SORT_ASC])->all(),'id','title'),['prompt'=>'Статус оплаты','options'=>$options]);
											?>
										</div>
										<?php 

											$sum_statesArray = ArrayHelper::map($sum_states,'id','id');
											$display = (in_array($paymentState->id, $sum_statesArray))?"block":"none";
										?>
										<div class="col-xs-4 sum_state_block" style="display: <?php echo $display;?>;">
										<?php echo $form->field($CustomerPayment, "sum",array('attribute'=>['class'=>"sum_hid"]))->textInput(['id' => 'sum_hid_'.$autotruck->id,'class'=>"form-control hid",'value'=>$CustomerPayment->sum]); ?>
										</div>

										<div class="col-xs-4">
										<?php echo Html::submitButton('Сохранить',['id'=>'submit_payment','class' => 'btn btn-primary', 'name' => 'customer-payment-autotruck']); ?>
										</div>
									</div>
									<div class="row">
										<div class="col-xs-10">
											<?php
												echo $form->field($CustomerPayment, "comment")->textarea();
											?>
										</div>
									</div>
									<?php ActiveForm::end();?>
								</div>
							</div>

							

							<div class="table autotruck_apps">
								<table class="table table-striped table-hover table-bordered">
								<tbody>
									<tr>
										<th>№</th>
										<th>Клиент</th>
										<th class="app_sender">Отправитель</th>
										<th class="app_place">Кол-во мест</th>
										<th class="app_package">Упаковка</th>
										<th>Наименование</th>
										<th>Вес (кг)</th>
										<th>Ставка ($)</th>
										<th>Сумма ($)</th>
										<th>Сумма (руб)</th>
										<th>Комментарий</th>
									</tr>
							<?php $cweight=0;$crate=0; $total = 0; $total_us = 0;
							foreach ($a['apps'] as $i=> $app) { ?>
									<tr>
										<td>
											<?php echo $i+1?>
											<?php
												$cl = $app->out_stock ? 'ok' : '';
											?>
											<span class="app_out_stock <?php echo $cl?>"></span>
										</td>
										<td><?=$app->buyer->name?></td>
										
										<?php 
														if(!$app->type){
														?>
															<td>
																<?php echo $app->sender 
																			? $app->senderObject->name 
																			: "Не указан"; 
																?>
															</td>
														
															<td><? echo $app->count_place ?></td>
														
															<td><?php echo $app->package ? $app->typePackaging->title : "Не указан"; ?></td>
														<?php	
														}else{
															?>
															<td colspan="3"></td>
															<?php
														}
													?>
										
										<td><?=$app->info?></td>
										<td><? echo $app->type?'':$app->weight?></td>
										<td><?=$app->rate?></td>
										
										<td><? echo $app->summa_us?> $</td>
										
										<td>
										    <?php 
										    
										        //echo $app->type ? round($app->rate*$autotruck->course,2) : round($app->weight*$app->rate*$autotruck->course,2);
										         $rate_vl = $app->weight > 0 ? $app->summa_us/$app->weight : 0;
										         $sum_ru = $app->weight * $rate_vl * $autotruck->course;
										         
										         echo $app->type ? round($app->rate*$autotruck->course,2) : round($sum_ru,2);
										     ?> 
										
										руб</td>
										
										<td><?=$app->comment?></td>
									</tr>
							<?php 
								$cweight += $app->type ? 0 : $app->weight; 
								//$total+= $app->type?$app->rate*$autotruck->course:$app->weight*$app->rate*$autotruck->course;
								$total+= $app->type? round($app->rate*$autotruck->course,2) : round($app->summa_us*$autotruck->course,2);
								$total_us+=$app->summa_us;  
								}?>
								<tr>
									<td colspan="3"><strong>Итого</strong></td>
									<td colspan="3"><strong><?php echo $autotruck->getAppCountPlace($client->id)?></strong></td>
									<td><strong><?php echo $cweight;?> кг.</strong></td>
									<td></td>
									<td><strong><?php echo round($total_us,2);?> $</strong></td>
									<td><strong><?php echo round($total,2);?> руб.</strong></td>
									<td></td>
								</tr>
								</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			<?php  $active = '';} ?>
		</div>
	<?php } ?>
				</div>
			</div>
<?php 

$script = <<<JS
			$("#btnClientStory").click(function(event){
				event.preventDefault();
				$("#modalJournal").modal('show').find(".modal-body").load($(this).attr('href'));
			});
JS;


$this->registerJs($script);

	Modal::begin([
		'header'=>"<h4>Журнал редактирования клиента</h4>",
		'id'=>'modalJournal',
		'size'=>'modal-all'
	]);
	Modal::end();
?>


<?php 

$script = <<<JS
		

JS;


$this->registerJs($script);

?>		
		
			<script id="source" language="javascript" type="text/javascript">
				$(function () {

				var grafik = [];
				<?php foreach ($grafik as $key => $v) { ?>
					grafik.push(['<?php echo date('M Y',strtotime($key));?>',<?php echo $v['weight'];?>]);
				<?php }?>	
    			
    			if(grafik.length){
    				var data = [{	
						data:grafik,
						label:" Количество веса по месяцам"
					}];

					var plot = $.plot("#placeholder", data, {
						series: {
							// bars: {
							// 	show: true,
							// 	barWidth: 0.2,
							// 	align: "center"
							// }
							lines: { show: true },
			        		points: { show: true }
						},
						grid: {clickable: true },
						xaxis: {
							mode: "categories",
							tickLength: 0
						}
					});


					$("#placeholder").bind("plotclick", function (event, pos, item) {
		        		if (item) {
		            		$("#grafik_info").html(" Вес " + item.series.data[item.dataIndex][1] + "кг <br>"+"Период " + item.series.data[item.dataIndex][0]);
		            		plot.highlight(item.series, item.datapoint);
		        		}
		    		});
    			}

				

				//при выборе статуса оплаты проверяем, если выбран частично оплачен. то отображаем поле для суммы
				$(".payment-state-select").change(function(){
					var this_val = parseInt($(this).val());
					var this_opt = $(this).find("option[value=\'"+this_val+"\']");
					if(this_opt.attr("data-sum")){
						$(this).parent().parent().siblings(".sum_state_block").show();
					}else{
						$(this).parent().parent().siblings(".sum_state_block").hide();
						
						$(this).parent().parent().siblings(".sum_state_block").find('input').val(0);
					}
				});

			

			});
			</script>

</div>
