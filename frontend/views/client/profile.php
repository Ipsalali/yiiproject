<?php 
use yii\helpers\Html;
use frontend\models\Autotruck;
use frontend\models\CustomerPayment;
use common\models\PaymentState;
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
				  		<div class="panel-heading">
				  			<div class="row">
				  			<div class="col-xs-9">
				  				<h4><?php echo $autotruck->name." №".$autotruck->id?>
				  				</h4>
				  			</div>
				  			<div class="col-xs-3" style="text-align:center">
				  				<span><?=date("d.m.Y",strtotime($autotruck->date))?></span>
				  				<?php echo Html::a("Скачать счет", array("client/mycheck","autotruck"=>$autotruck->id),array("class"=>"check btn btn-success"))?>
				  			</div>
				  			</div>

				  		</div>
						<div class="panel-body autotruck_info">
							
							<div class="row">
								<div class="col-xs-4">
									<p>Курс: <span><?php echo $autotruck->course; ?> руб.</span></p>
									<p>Страна поставки: <span><?php echo $autotruck->countryName; ?></span></p>
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
								</div>
								<div class="col-xs-4">
									<div class="col-xs-12">
										<p>Текущий статус: <span style="color:<?=$paymentState->color?>"><?php echo $paymentState->title; ?></span></p>
										<?php
											if($paymentState->id == PaymentState::getSumState()->id){
										?><p>Сумма: <?php echo $CustomerPayment->sum?> $</p><?php }?>
										<p>Комментраий: <?php echo $CustomerPayment->comment?></p>
									</div> 
								</div>
							</div>

							

							<div class="table autotruck_apps">
								<h4>Ваши товары:</h4>
								<table class="table table-striped table-hover table-bordered">
								<tbody>
									<tr>
										<th>№</th>
										<th>Информация</th>
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
										<td><?=$app->info?></td>
										<td><?=$app->weight?></td>
										<td><?=$app->rate?></td>
										<td><?=$app->weight*$app->rate?> $</td>
										<td><?=$app->weight*$app->rate*$autotruck->course?> руб</td>
										<td><?=$app->comment?></td>
									</tr>
							<?php   $cweight += $app->weight; 
									$totalUs+=$app->weight*$app->rate;
									$total+=$app->weight*$app->rate*$autotruck->course; 
								}?>
								<tr>
									<td colspan="2"><strong>Итого</strong></td>
									<td><strong><?php echo $cweight;?> кг.</strong></td>
									<td></td>
									<td><strong><?php echo $totalUs;?> $</strong></td>
									<td><strong><?php echo $total;?> руб.</strong></td>
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