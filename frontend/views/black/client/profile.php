<?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use frontend\models\Autotruck;
use common\models\TypePackaging;

$this->title = "TEDTRANS";
$user = Yii::$app->user->identity;
$packages = TypePackaging::find()->all();
$canFormAutotruck = Yii::$app->user->can("autotruck/create");
?>

<div class="card" style="margin-bottom: 50px;">
	<div class="card-header card-header-primary">
		<h2 class="card-title">Личный кабинет</h2>
	</div>
	<div class="card-body">
		<div class="row">
	 		<div class="col-7">
	 			<div class="row">
					<div class="col-5 client_manager">
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
							<p>Вы не закреплены к менеджеру.</p>
						<?php } ?>
					</div>
				</div>
				<div class="row">
					<div class="col-12">
						<p>Задолженность: <?php echo $client->user->getManagerSverka();?> $</p>
					</div>		
				</div>
			</div>
		</div>
	</div>
</div>

<div class="card card-plain">
	<div class="card-header card-header-primary">
		<h3 class="card-title">Ваши заявки:</h3>
	</div>
	<div class="card-body">
		<?php if($autotrucks){ ?>
			<?php  foreach ($autotrucks as $key => $autotruck){ ?>
				<div class="card">
					<div class="card-header">
					  	<div class="row">
					  		<div class="col-9">
					  			<h4>
					  				<?php echo $autotruck->name; ?>
					  				    &nbsp&nbsp&nbsp
					  				<?php 
					  					echo ($user->id === $autotruck->creator && $canFormAutotruck) ? html::a("Редактировать",array("autotruck/form","id"=>$autotruck->id)) : "";
					  				?>
					  			</h4>
					  		</div>
					  		<div class="col-3 text-right">
					  			<span><?php echo date("d.m.Y",strtotime($autotruck->date));?></span>
					  			<?php echo Html::a("Скачать счет", array("client/mycheck","autotruck"=>$autotruck->id),array("class"=>"check btn btn-success"));?>
					  		</div>
					  	</div>
					</div>
					
					<div class="card-body">
						<div class="row">
							<div class="col-6">
								<p><strong>Курс:</strong><span><?php echo Html::encode($autotruck->course); ?> руб.</span></p>
								<p><strong>Страна поставки:</strong> <span><?php echo Html::encode($autotruck->countryName); ?></span></p>
								<p><strong>Номер машины:</strong> <?php echo Html::encode($autotruck->auto_number);?></p>
	                            <p><strong>ГТД:</strong> <?php echo Html::encode($autotruck->gtd);?></p>
							</div>
							<div class="col-4">
								<h4>Статус:</h4>
									<ul>
										<?php
											$story = $autotruck->traceStory;
											if(is_array($story)){
												foreach ($story as $s) { 
													$active_s = ($s->status_id == $autotruck->status) ? "active_status" : '';
										?>
													<li class="app_status <?php echo $active_s?>">
														<?php echo Html::encode($s->status->title);?>
														<span><?php echo date('d.m.Y',strtotime($s->trace_date))?></span>
													</li>
											<?php }
											}
										?>
									</ul>
								<div>
								<?php
									$autotuckPackages = $autotruck->packagesCountPlace;
										if(is_array($packages) && is_array($autotuckPackages)){
											foreach ($packages as $key => $package) {
												$count = array_key_exists($package->id, $autotuckPackages) ? $autotuckPackages[$package->id]['count'] : 0;
												if($count > 0){
												?>
													<p>
														<?php echo Html::encode($package->title); ?>: <?php echo $count; ?>
													</p>
												<?php
												}
											}
										}

										if(array_key_exists("none", $autotuckPackages) && $autotuckPackages['none']['count'])
										{ ?>
										<p>Не известная упаковка: <?php echo $autotuckPackages['none']['count']; ?></p>
										<?php } ?>
									
								</div>
							</div>
						</div>
								

						<div class="row">
							<div class="col-12">
								<!-- <h4>Ваши товары:</h4> -->
								<table class="table table-sm table-striped table-hover table-bordered">
									<thead>
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
									</thead>
									<tbody>
									<?php 
										$cweight=0;$crate=0; $total = 0; $total_us = 0;
										foreach ($autotruck->appsCollection as $i=> $app) { ?>
											<tr>
												<td><?php echo $i+1;?></td>
												<?php if(!$app['type']){ ?>
													<td>
													<?php echo $app['sender'] ? $app['senderName'] : "Не указан"; ?>
													</td>
																
													<td><?php echo $app['count_place'] ?></td>
													<td><?php echo $app['package'] ? $app['packageTitle'] : "Не указан"; ?></td>
												<?php }else{ ?>
													<td colspan="3"></td>
												<?php } ?>
												<td><?php echo $app['info']; ?></td>
												<td><?php echo $app['type']? '' : $app['weight']; ?></td>
												<td><?php echo $app['rate']; ?></td>
												<td><?php echo $app['summa_us']; ?> $</td>
												<td>
												    <?php 
												        $rate_vl = $app['weight'] > 0 ? $app['summa_us']/$app['weight'] : 0;
												        $sum_ru = $app['weight'] * $rate_vl * $autotruck['course'];
												         
												        echo $app['type'] ? round($app['rate']*$autotruck['course'],2) : round($sum_ru,2);
												     ?> 
												руб
												</td>
												<td><?php echo $app['comment']?></td>
											</tr>
									<?php   
											$cweight += $app['type'] ? 0 : $app['weight']; 
											$total+= $app['type']? round($app['rate']*$autotruck->course,2) : round($app['summa_us']*$autotruck->course,2);
											$total_us+=$app['summa_us'];  
										}
									?>
										<tr>
											<td colspan="2"><strong>Итого</strong></td>
											<td colspan="3"><strong><?php echo $autotruck->totalCountPlace;?></strong></td>
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
			</div>
		<?php } ?>
	<?php } ?>
</div>

		
