<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use frontend\models\App;
use frontend\models\Autotruck;
use frontend\models\ExpensesManager;
use common\models\User;
use yii\helpers\Url;


$roleexpenses = 'autotruck/addexpenses';
$expensesManager = new ExpensesManager;
$AutotruckExpenses =ExpensesManager::getAutotruckExpenses($autotruck->id);
$Autotrucks = Autotruck::find()->orderBy('id')->all();
?>



<div class="base_content">
	<div class="row">
		<div class="col-xs-12">
			<h1>Заявка: <?=$autotruck->name?></h1>
			 
		</div>
	</div>
	<?php if(Yii::$app->session->hasFlash('ExpensesManagerAddSuccess')): ?>
		<div class="alert alert-success">
   			Расход записан.
		</div>
	<?php endif; ?>

	<?php if(Yii::$app->session->hasFlash('ExpensesManagerAddError')): ?>
		<div class="alert alert-error">
   			Не удалось записать расход.
		</div>
	<?php endif; ?>

	<?php if(Yii::$app->session->hasFlash('AutotruckSaved')): ?>
	<div class="alert alert-success">
    	Заявка сохранена!
	</div>
	<?php endif; ?>


	<?php if($autotruck){?>
		<div class="app_blocks">
				<div id="autotruck_tab_<?=$autotruck->id?>" class="">
				  	<div class="panel panel-primary">
				  		<div class="panel-heading">
				  			<div class="row">
				  				<div class="col-xs-6">
				  					<?=$autotruck->name?> №<?=$autotruck->id?>
				  				</div>
				  				<div class="col-xs-6" style="text-align:right;padding-right:20px;">
				  					<span>Дата: <?=date("d.m.Y",strtotime($autotruck->date))?></span>
				  				</div>
				  			</div>
				  		</div>

						<div class="panel-body autotruck_info" style="padding:5px;">
							<div class="row">
								<div class="col-xs-3">
									<p>Страна поставки: <span><?php echo $autotruck->supplierCountry->country?></span></p>
								</div>
								<div class="col-xs-3">
									<p>Курс: <span><?php echo $autotruck->course?> руб.</span></p>
								</div>
								
							</div>
							<div class="row" style="margin-top:20px;">
								<div class="col-xs-3">
									<p>Статус:</p>
									<ul>
										<?php
											$autotruck->activeStatus->title;
											$story = $autotruck->traceStory;
											if(is_array($story)){
												foreach ($story as $key => $s) { 
													$active_s = ($s->status_id == $autotruck->status)? "active_status" :'';
										?>
													
											<li class="app_status <?=$active_s?>">
												<?=$s->status->title?>
												<span><?=date('d.m.Y',strtotime($s->trace_date))?></span>

												<?php 
													
													echo Html::a("x",['autotruck/status-remove','id'=>$s->trace_id,'aid'=>$autotruck->id],['style'=>'color:#f00','alt'=>'Удалить','title'=>'Удалить','data-confirm'=>'Подтвердите свои действия!']);
													
												?>

											</li>
										<?php	} } ?>
									</ul>
								</div>
								<div class="col-xs-3">
									<div>
										<p>Описание:</p>
										<?=$autotruck->description?>
									</div>
								</div>
								
							</div>
							<div class="app_update_btn">
											<?php echo Html::a('Редактировать', array('autotruck/update','id'=>$autotruck->id), array('class' => 'btn btn-default')); ?>
							</div>
							<ul class="nav nav-tabs">
  								<li class="active"><a data-toggle="tab" href="#apps">Наименования</a></li>

								<?php if(Yii::$app->user->can($roleexpenses)){?>
  									<li><a data-toggle="tab" href="#expenses">Расходы</a></li>
  								<?php } ?>
							</ul>
							<div class="tab-content">
								<div id="apps" class="tab-pane fade in active">
										

										<div class="table autotruck_apps">
											<table class="table table-striped table-hover table-bordered">
											<tbody>
												<tr>
													<th>№</th>
													<th class="app_client">Клиент</th>
													<th class="app_info">Информация</th>
													<th>Вес (кг)</th>
													<th>Ставка ($)</th>
													<th>Сумма ($)</th>
													<th>Сумма (руб)</th>
													<th>Комментарий</th>
												</tr>
										<?php $cweight=0; $total = 0; $total_us = 0;
										foreach ($autotruck->getApps() as $key => $app) { ?>
												<tr>
													<td><?=$key+1?></td>
													<td><?php echo Html::a($app->buyer->name,['client/read','id'=>$app->client],array('target'=>'_blank'));?></td>
													<td><?=$app->info?></td>
													<td><? echo $app->type?'':$app->weight?></td>
													<td><?=$app->rate?></td>
													<td><? echo $app->type ? $app->rate: round($app->weight*$app->rate,2)?> $</td>
													<td><? echo $app->type ? round($app->rate*$autotruck->course,2) : round($app->weight*$app->rate*$autotruck->course,2)?> руб</td>
													
													<td><?=$app->comment?></td>
												</tr>
										<?php 
											$cweight += $app->type ? 0 : $app->weight; $total+= $app->type?$app->rate*$autotruck->course:$app->weight*$app->rate*$autotruck->course;
											$total_us+=$app->type?$app->rate:$app->weight*$app->rate;
										 }?>
										<tr>
												<td colspan="3"><strong>Итого</strong></td>
												<td><strong><?php echo round($cweight,2);?> кг.</strong></td>
												<td></td>
												<td><strong><?php echo round($total_us,2);?> $</strong></td>
												<td><strong><?php echo round( $total,2);?> руб.</strong></td>
												<td></td>
											</tr>
											</tbody>
											</table>
										</div>
								</div> <!-- Контент наименования -->
								<?php if(Yii::$app->user->can($roleexpenses)){?>
								<div id="expenses" class="tab-pane fade in">
									<div class="row">
										<div class="col-xs-12">
											<?php 
											if(count($AutotruckExpenses)){
												$dataProvider = new ActiveDataProvider([
	           										'query' => ExpensesManager::find()->where(['autotruck_id'=>$autotruck->id]),
	            								]);
	            								echo GridView::widget([
	            										'dataProvider' => $dataProvider,
	            										'summary'=>"",
	            										'columns'=>[
	            											['class'=>'yii\grid\SerialColumn'],
	            											[
	            												'attribute'=>'manager_id',
	            												'value'=>'manager.name'
	            											],
	            											'cost',
	            											'comment'
	            										]
	            									]);
											} ?>
										</div>
									</div>
								</div>
								<?php }?>
							</div>
						</div>
					</div>
				</div>
		</div>
	<?php } ?>
</div>
