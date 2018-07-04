<?php

use yii\bootstrap\NavBar;
use yii\helpers\Html;
use frontend\models\Autotruck;

$this->title = "Клиент ".$client->name;

?>

<div>
	<h3>Заявки клиента <?=$client->name?></h3>
</div>

<?php if(Yii::$app->session->hasFlash('Notification_error')): ?>
<div class="alert alert-error">
    Не удолось отправить уведомление.
</div>
<?php endif; ?>

<?php if(Yii::$app->session->hasFlash('Notification_sended')): ?>
<div class="alert alert-success">
    Уведомление отправлено.
</div>
<?php endif; ?>

<?php if($autotrucks){?>
		<div class="app_blocks">
			<?php  foreach ($autotrucks as $key => $a){
				if($autotruck = Autotruck::find()->where("id=".$key)->one()){

				}else continue;

			?>
				<div id="autotruck_tab_<?=$key?>">
				  	<div class="panel panel-primary">
				  		<div class="panel-heading">
				  			<?=$autotruck->name?> №<?=$autotruck->id?>
				  			<span>Дата: <?=date("d.m.Y",strtotime($autotruck->date))?></span>
				  			
				  			<?php //echo Html::a("Сформировать счет", array("client/check","client"=>$client->id,"autotruck"=>$autotruck->id),array("class"=>"check"))?>
				  			<?php //echo Html::a("Отправить уведомления", array("client/sendnotification","client"=>$client->id,"autotruck"=>$autotruck->id),array("class"=>"notification check"))?>

				  		</div>

						<div class="panel-body autotruck_info">
							
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
											<?		}
												}
								?>
							</ul>

							<div class="table autotruck_apps">
								<table class="table table-striped table-hover table-bordered">
								<tbody>
									<tr>
										<th>№</th>
										<th>Клиент</th>
										<th>Информация</th>
										<th>Вес</th>
										<th>Ставка</th>
										<th>Курс</th>
										<th>Комментарий</th>
									</tr>
							<?php 
							foreach ($a['apps'] as $i=> $app) { ?>
									<tr>
										<td><?=$i+1?></td>
										<td><?=$app->buyer->name?></td>
										<td><?=$app->info?></td>
										<td><?=$app->weight?></td>
										<td><?=$app->rate?></td>
										<td><?=$app->course?></td>
										<td><?=$app->comment?></td>
									</tr>
							<?php }?>
								</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			<?php  $active = '';} ?>
		</div>
	<?php } ?>