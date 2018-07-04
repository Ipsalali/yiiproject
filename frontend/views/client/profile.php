<?php 
use yii\helpers\Html;
use frontend\models\Autotruck;
use frontend\models\CustomerPayment;
use common\models\PaymentState;
use yii\helpers\ArrayHelper;
use common\models\TypePackaging;
$this->title = "TEDTRANS";

$user = Yii::$app->user->identity;
$packages = TypePackaging::find()->all();
?>

<div class="client_page">
	<div class="row">
	 	<div class="col-xs-7">
	 		<div class="row">
				<div class="col-xs-5 client_manager">
					<h4>Ваш менеджер:</h4>
					<?php $class=""; if($client->managerUser){ 
						$class="manager_info";
						?>
						<div class="manager_info_block">
							<?php if($client->managerUser->name){?>
								<p><span>Имя: </span><strong><?php echo $client->managerUser->name;?></strong></p>
							<?php } ?>
							<?php if($client->managerUser->phone){?>
								<p><span>Телефон: </span><strong><?php echo $client->managerUser->phone;?></strong></p>
							<?php } ?>
							<?php if($client->managerUser->email){?>
								<p><span>E-mail: </span><strong><?php echo $client->managerUser->email;?></strong></p>
							<?php } ?>
							
						</div>
					<?php }else{ ?> 
						<p>ВЫ не закреплены к менеджеру.</p>
					<? } ?>
				</div>
			</div>
		
		</div>
		
		
			<div class="col-xs-12">
				<p>Задолженность: 
				    <?php 
				    	echo $client->user->getManagerSverka();
				        //echo $client->getDebt() - $client->getSumStateSum();
				    ?> $
				</p>
			</div>
		
		
		<div class="row">
			<div class="col-xs-12">
				<h3>Ваши заявки:</h3>
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
							$CustomerPayment = CustomerPayment::getCustomerPayment($client->id,$autotruck->id);
							$paymentState = $CustomerPayment->id ? $CustomerPayment->paymentState : PaymentState::getDefaultState();
						?>
				<div id="autotruck_tab_<?=$key?>" >
				  	<div class="panel panel-primary">
				  		<div class="panel-heading profile_autotruck_head">
				  			<div class="row">
				  			<div class="col-xs-9">
				  				<h4>
				  				    <?php echo $autotruck->name; ?>
				  				    &nbsp&nbsp&nbsp
				  					<?php 
				  						echo ($user->id === $autotruck->creator) ? html::a("Редактировать",array("autotruck/update","id"=>$autotruck->id)) : "";
				  					?>
				  				</h4>
				  			</div>
				  			<div class="col-xs-3" style="text-align:right">
				  				<span><?=date("d.m.Y",strtotime($autotruck->date))?></span>
				  				<?php echo Html::a("Скачать счет", array("client/mycheck","autotruck"=>$autotruck->id),array("class"=>"check btn btn-success"))?>
				  			</div>
				  			</div>

				  		</div>
						<div class="panel-body autotruck_info profile_autotruck_body">
							
							<div class="row">
								<div class="col-xs-4">
									<p><strong>Курс:</strong><span><?php echo $autotruck->course; ?> руб.</span></p>
									<p><strong>Страна поставки:</strong> <span><?php echo $autotruck->countryName; ?></span></p>
									<p><strong>Номер машины:</strong> <?php echo $autotruck->auto_number?></p>
                                    <p><strong>ГТД:</strong> <?php echo Html::encode($autotruck->gtd)?></p>
								</div>
								<div class="col-xs-4">
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
								<div class="col-xs-4" style="display: none;">
									<div class="col-xs-12">
										<p><strong>Статус оплаты:</strong> <span style="color:<?=$paymentState->color?>"><?php echo $paymentState->title; ?></span></p>
										<?php
										    $sum_states = PaymentState::getSumStates();
										    $sum_statesArray = ArrayHelper::map($sum_states,'id','id');
											if(in_array($paymentState->id, $sum_statesArray)){
										?>
											<p><strong>Сумма:</strong> <?php echo $CustomerPayment->sum?> $</p>
										<?php }?>
										<p><strong>Итого кол-во мест:</strong> <?php echo $autotruck->getAppCountPlace($client->id)?></p>
										<p><strong>Комментрий:</strong> <?php echo $CustomerPayment->comment?></p>
									</div> 
								</div>
							</div>
							

							<div class="table autotruck_apps">
								<!-- <h4>Ваши товары:</h4> -->
								<table class="table table-striped table-hover table-bordered">
								<tbody>
									<tr>
										<th>№</th>
										<th class="app_sender">Отправитель</th>
										<th class="app_place">Кол-во мест</th>
										<th class="app_package">Упаковка</th>
										<th>Наименование</th>
										<th>Вес (кг)</th>
										<th>Ставка ($)</th>
										<th>Сумма $</th>
										<th>Сумма (руб)</th>
										<th>Комментарий</th>
									</tr>
							<?php $cweight=0;$crate=0; $total = 0; $totalUs = 0;
							foreach ($a['apps'] as $i=> $app) { ?>
									<tr>
										<td><?=$i+1?></td>
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
										<td><?=$app->weight?></td>
										<td><?=$app->rate?></td>
										<td><?=round($app->weight*$app->rate,2)?> $</td>
										<td><?=round($app->weight*$app->rate*$autotruck->course,2)?> руб</td>
										<td><?=$app->comment?></td>
									</tr>
							<?php   $cweight += $app->weight; 
									$totalUs+=$app->weight*$app->rate;
									$total+=$app->weight*$app->rate*$autotruck->course; 
								}?>
								<tr>
									<td colspan="2"><strong>Итого</strong></td>
									<td colspan="3"><strong><?php echo $autotruck->getAppCountPlace($client->id)?></strong></td>
									<td><strong><?php echo $cweight;?> кг.</strong></td>
									<td></td>
									<td><strong><?php echo round($totalUs,2);?> $</strong></td>
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
		</div>
		
			

</div>