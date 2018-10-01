<?php 

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
use frontend\models\App;
use frontend\models\Autotruck;
use frontend\models\ExpensesManager;
use common\models\User;
use yii\helpers\Url;
use common\models\TypePackaging;
use frontend\bootstrap4\Modal;

$roleexpenses = 'autotruck/addexpenses';
$userCanRoleexpenses = Yii::$app->user->can($roleexpenses);
$expensesManager = new ExpensesManager;

$AutotruckExpenses =ExpensesManager::getAutotruckExpenses($autotruck->id);

$Autotrucks = Autotruck::find()->orderBy('id')->all();

$this->title = $autotruck->name;
$this->params['breadcrumbs'][] = ['label'=>"Список заявок",'url'=>Url::to(['autotruck/index'])];
$this->params['breadcrumbs'][] = $this->title;

$packages = TypePackaging::find()->all();
$packagesIndexed = ArrayHelper::map($packages,'id','title');

$appCountPlace = $autotruck->appCountPlace;
?>
<?php if($autotruck){?>
<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-header card-header-primary">
				<div class="row">
					<div class="col-6">
						<h3><?php echo $autotruck->name?> №<?php echo $autotruck->id?></h3>
					</div>
					<div class="col-6" style="text-align:right;padding-right:20px;">
					  	<span>Дата: <?php echo date("d.m.Y",strtotime($autotruck->date))?></span>
					</div>
				</div>
			</div>

			<div class="card-body">
				<div class="row">
					<div class="col-3">
						<p>Страна поставки: <span><?php echo $autotruck->country ? $autotruck->supplierCountry->country : "Не указана"?></span></p>
					</div>
					<div class="col-3">
						<p>Курс: <span><?php echo $autotruck->course?> руб.</span></p>
					</div>
				</div>
				<div class="row" style="margin-top:20px;">
					<div class="col-3">
						<p>Статус:</p>
						<ul>
						<?php
							$story = $autotruck->traceStory;
							if(is_array($story)){
								foreach ($story as $key => $s) { 
									$active_s = ($s->status_id == $autotruck->status)? "active_status" :'';
						?>
													
							<li class="app_status <?=$active_s?>">
								<?php echo $s->status->title?>
									<span><?php echo date('d.m.Y',strtotime($s->trace_date))?></span>
							</li>
						<?php	} } ?>
						</ul>
					</div>
					<div class="col-3">
						<div>
							<p>Описание:</p>
										<?php echo Html::encode($autotruck->description)?>
						</div>
					</div>
					<div class="col-3">
						<div>
							<p>Прикрепленный файл:</p>
							<?php 
							if($autotruck->file){
								$files = explode('|', $autotruck->file);
								foreach ($files as $key => $file) {
									if($file && file_exists('uploads/'.$file)){
														
										echo Html::a($file,['autotruck/download','id'=>$autotruck->id,'file'=>$file],['target'=>'_blank']),'   ',
											Html::a("x",['autotruck/unlinkfile','id'=>$autotruck->id,'file'=>$file],['data-confirm'=>'Подтвердите удаление файла']),
											"<br>";
														
										}
								}
							} 
							?>
						</div>
						<div>
							<p>Итого кол-во мест: <?php echo $appCountPlace;?></p>
							<?php
								if(is_array($packages)){
									foreach ($packages as $key => $package) {
										$count = $autotruck->getAppCountPlacePackage($package->id);

										if($count > 0){ ?>
											<p><?php echo $package->title?>: <?php echo $count; ?></p>
										<?php
										}
									}
								}
							?>
						</div>
					</div>
					<div class="col-3">
						<p>Номер машины: <?php echo Html::encode($autotruck->auto_number)?></p>
						<p>Транспорт: <?php echo Html::encode($autotruck->auto_name)?></p>
						<p>ГТД: <?php echo Html::encode($autotruck->gtd)?></p>
						<p>Оформление: <?php echo Html::encode($autotruck->decor)?></p>
					</div>
				</div>
				<div class="row" style="margin-bottom: 20px;">
					<div class="col-12">
					<?php echo Html::a('Редактировать', array('autotruck/form','id'=>$autotruck->id), array('class' => 'btn btn-default')); ?>

					<?php echo Html::a('Выгрузить в excel', array('autotruck/to-excel','id'=>$autotruck->id), array('class' => 'btn btn-success')); ?>

					<?php echo Html::a("Журнал редактирования заявки",['autotruck/autotruck-story','id'=>$autotruck->id],['id'=>'btnAutotruckStory','class'=>'btn btn-success'])?>
					</div>
				</div>

				<div class="card-header card-header-tabs card-header-primary">
					<div class="nav-tabs-navigation">
						<ul class="nav nav-tabs" role='tablist'>
  							<li class="nav-item active"><a class='nav-link active' data-toggle="tab" href="#apps">Наименования</a></li>

							<?php if($userCanRoleexpenses){?>
  								<li class="nav-item"><a class='nav-link' data-toggle="tab" href="#expenses">Расходы</a></li>
  							<?php } ?>
						</ul>
					</div>
				</div>
					
				
				<div class="tab-content">
					<div id="apps" class="tab-pane active">
						<div class="card-body">
							<table class="table table-sm table-striped table-hover table-bordered">
								<thead>
										<tr>
											<th>№   
												<?php 
															$autotruckApps = $autotruck->getAppsArray();
															$c = ($autotruck->getCountOutStockApp() == count($autotruckApps)) ? 1 : 0;

															echo Html::checkbox('out_stock_all',$c,['id'=>'out_stock_all','value'=>$autotruck->id]);
														?>
											</th>
												<th class="app_client">Клиент</th>
												<th class="app_sender">Отправитель</th>
												<th class="app_info">Наименование</th>
												<th class="app_place">Кол-во мест</th>
												<th class="app_package">Упаковка</th>
												<th>Вес (кг)</th>
												<th>Ставка ($)</th>
												<th>Сумма ($)</th>
												<th>Сумма (руб)</th>
												<th>Комментарий</th>
												<th></th>
											</tr>
								</thead>
								<tbody>
										<?php $cweight=0; $total = 0; $total_us = 0;
										foreach ($autotruckApps as $key => $app) { ?>
												<tr>
													<td>
													<?php echo $key+1?>
													<?php 
														echo Html::checkbox('out_stock[]',$app['out_stock'],['value'=>$app['id'],'class'=>'out_stock_item']);
													?>
													</td>
													<td>
													<?php 
														echo $app['client'] ? Html::a($app['client_name'],['client/read','id'=>$app['client']],array('target'=>'_blank')) : "";
													?>		
													</td>
													
													<?php 
														if(!$app['type']){
														?>
															<td>
																<?php echo $app['sender'] 
																			? $app['sender_name'] 
																			: "Не указан"; 
																?>
															</td>
														    <td><?php echo $app['info']?></td>
															<td><?php echo $app['count_place'] ?></td>
														
															<td>
																<?php 
																	echo $app['package'] && array_key_exists($app['package'], $packagesIndexed) ? $packagesIndexed[$app['package']] : "Не указан"; 
																?>		
															</td>
														<?php	
														}else{
															?>
															<td></td>
															<td><?php echo $app['info']?></td>
															<td colspan="2"></td>
															<?php
														}
													?>
													
													
													
													<td><?php echo $app['type'] ? '': $app['weight']?></td>
													<td><?php echo $app['rate']?></td>
													<td><?php echo $app['summa_us']; ?> $</td>
													<td>
														<?php 
															$rate_vl = $app['weight'] > 0 ? $app['summa_us']/$app['weight'] : 0;
															$sum_ru = $app['weight'] * $rate_vl * $autotruck->course;

															echo $app['type'] ? round($app['rate']*$autotruck->course,2) : round($sum_ru,2);
														?> 

														руб
													</td>
													
													<td><?php echo $app['comment']?></td>
													<td style="text-align: center;"><?php echo Html::a("Журнал",['autotruck/app-story','id'=>$app['id']],['class'=>'btnAppStory'])?></td>
												</tr>
										<?php 
											$cweight += $app['type'] ? 0 : $app['weight']; 

											$total+= $app['type']? round($app['rate']*$autotruck->course,2) : round($app['summa_us']*$autotruck->course,2);

											$total_us+=$app['summa_us'];
										 }?>
											<tr>
												<td colspan="4"><strong>Итого</strong></td>
												<td colspan="2"><strong><?php echo $appCountPlace?></strong></td>
												<td><strong><?php echo round($cweight,2);?> кг.</strong></td>
												<td></td>
												<td><strong><?php echo round($total_us,2);?> $</strong></td>
												<td><strong><?php echo round( $total,2);?> руб.</strong></td>
												<td></td>
												<td></td>
											</tr>
											</tbody>
									</table>
								</div>
							</div> <!-- Контент наименования -->

							<?php
								$script = <<<JS

									$(".out_stock_item").change(function(event){
										if($(this).prop("checked")){

												var tItem = $(this);
												$.ajax({
													url:'index.php?r=autotruck/set-out-stock&id='+parseInt($(this).val())+'&value=1',
													method: 'GET',
													dataType:'json',
													success:function(json){
														if(parseInt(json['result'])){
															tItem.prop("checked",1);
														}else{
															tItem.prop("checked",0);
														}
													}
												})
											}else{
												var tItem = $(this);
												$.ajax({
													url:'index.php?r=autotruck/set-out-stock&id='+parseInt($(this).val())+'&value=0',
													method: 'GET',
													dataType:'json',
													success:function(json){
														if(parseInt(json['result'])){
															tItem.prop("checked",0);
														}else{
															tItem.prop("checked",1);
														}
													}
												})
											}



											event.preventDefault();
										});


										$("#out_stock_all").change(function(event){

											if($(this).prop("checked")){

												var tItem = $(this);
												$.ajax({
													url:'index.php?r=autotruck/set-all-out-stock&id='+parseInt($(this).val())+'&value=1',
													method: 'GET',
													dataType:'json',
													success:function(json){
														if(parseInt(json['result'])){
															tItem.prop("checked",1);
															$(".out_stock_item").prop("checked",1);
														}else{
															tItem.prop("checked",0);
															$(".out_stock_item").prop("checked",0);
														}
													}
												})
											}else{
												var tItem = $(this);
												$.ajax({
													url:'index.php?r=autotruck/set-all-out-stock&id='+parseInt($(this).val())+'&value=0',
													method: 'GET',
													dataType:'json',
													success:function(json){
														if(parseInt(json['result'])){
															tItem.prop("checked",0);
															$(".out_stock_item").prop("checked",0);
														}else{
															tItem.prop("checked",1);
															$(".out_stock_item").prop("checked",1);
														}
													}
												})
											}

											event.preventDefault();
										})
JS;
									$this->registerJs($script);
								?>


								<?php if($userCanRoleexpenses){?>
							<div id="expenses" class="tab-pane">
								<div class="card-body">
									<div class="row">
										<div class="col-12">
											<?php 
											
												$dataProvider = new ArrayDataProvider([
	           										'allModels' => $AutotruckExpenses,
	            								]);
	            								echo GridView::widget([
	            										'dataProvider' => $dataProvider,
	            										'summary'=>"",
	            										'tableOptions'=>['class'=>'table table-sm table-striped table-bordered table-hover'],
	            										'columns'=>[
	            											['class'=>'yii\grid\SerialColumn'],
	            											[
	            												'attribute'=>'date',
	            												'value'=>function($e){
	            													return date("d.m.Y",strtotime($e->date));
	            												}
	            											],
	            											[
	            												'attribute'=>'manager_id',
	            												'value'=>'manager.name'
	            											],
	            											'cost',
	            											'comment',
	            											['class' => 'yii\grid\ActionColumn',
													         'template' => '{view}',
													         'buttons' =>
													             [
													                 'view' => function ($url, $model) {
													                    return Html::a("Журнал",['autotruck/expenses-story','id'=>$model['id']],['class'=>'btnExpensesStory']); 
													                },
												]
											],
	            						]
	            					]);
								?>
								</div>
							</div>
						</div>
					</div>
				<?php }?>
			</div>
		</div>
	</div>
<?php 

	$script = <<<JS
			$("#btnAutotruckStory").click(function(event){
				event.preventDefault();
				$("#modalJournalAutotruck").modal('show').find(".modal-body").load($(this).attr('href'));
			});


			$(".btnAppStory").click(function(event){
				event.preventDefault();
				$("#modalJournalApp").modal('show').find(".modal-body").load($(this).attr('href'));
			});

			$(".btnExpensesStory").click(function(event){
				event.preventDefault();
				$("#modalJournalExpenses").modal('show').find(".modal-body").load($(this).attr('href'));
			});
JS;


	$this->registerJs($script);

		Modal::begin([
			'header'=>"Журнал редактирования заявки",
			'id'=>'modalJournalAutotruck',
			'class'=>'modal-all',
			'size'=>'modal-all'
		]);
		Modal::end();


		Modal::begin([
			'header'=>"Журнал редактирования наименований",
			'id'=>'modalJournalApp',
			'size'=>'modal-all'
		]);
		Modal::end();

		Modal::begin([
			'header'=>"Журнал редактирования наименований",
			'id'=>'modalJournalExpenses',
			'size'=>'modal-all'
		]);
		Modal::end();
	?>
	</div>
</div>
<?php } ?>

