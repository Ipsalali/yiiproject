<?php

use yii\helpers\Html;
use yii\helpers\Url;
use frontend\bootstrap4\Modal;
use yii\bootstrap\ActiveForm;
use common\models\Currency;

$this->title = $model->name;

$this->params['breadcrumbs'][] = ['label'=>"Список переводов",'url'=>Url::to(['transferspackage/index'])];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
	<div class="card-header card-header-primary">
		<div class="row">
			<div class="col">
				<h3>
					<?php echo $this->title?>
				</h3>
			</div>
			<div class="col text-right">
				<span>Дата: <?php echo date("d.m.Y",strtotime($model->date))?></span>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="row">
				<div class="col-3">
					<p>Статус: <?php echo Html::encode($model->statusTitle)?> </p>
					<p>Дата изменения статуса: <?php echo date("d.m.Y",strtotime($model->status_date))?></p>
				</div>
				<div class="col-2">
					<?php echo Html::a("Изменить статус",['transferspackage/change-status','id'=>$model->id],['id'=>'btnChangeStatus','class'=>'btn btn-success'])?>
					<?php echo Html::a("История изменений статуса",['transferspackage/story-status','id'=>$model->id],['id'=>'btnStoryStatus','class'=>'btn btn-success'])?>
				</div>
				
				<div class="col-3">
					<?php 
						$files = explode("|", $model->files);
						if(count($files) && (count($files) > 1 || $files[0] != "" && $files[0] != " ")){

							echo Html::a("Посмотреть файлы",["transferspackage/show-files",'id'=>$model->id],['id'=>'btnShowFiles','class'=>"btn btn-success"]);
						}else{
							?>
							<p>Нет приложенных файлов</p>
							<?php
						}
					?>
				</div>
		</div>
		<div class="row">
			<div class="col-3">
				<p>Комментарий:</p>
				<p><?php echo Html::encode(nl2br($model->comment))?></p>
			</div>
		</div>
		<div class="row" style="margin-bottom: 20px;">
			<div class="col-12">
				<?php echo Html::a('Редактировать', array('transferspackage/form','id'=>$model->id), array('class' => 'btn btn-default')); ?>

				<?php //echo Html::a("Журнал редактирования перевода",['transferspackage/transferspackage-story','id'=>$model->id],['id'=>'btnAutotruckStory','class'=>'btn btn-success']);?>
			</div>
		</div>
		<div class="row">
			<div class="col-12">
		        <div class="card-header card-header-primary">
					<div class="nav-tabs-navigation">
						<ul class="nav nav-tabs" role='tablist'>
	  						<li class="nav-item"><a class='nav-link active' data-toggle="tab" href="#transfers">Услуги</a></li>
	  						<li class="nav-item"><a class='nav-link' data-toggle="tab" href="#expenses">Расходы</a></li>
						</ul>
					</div>
				</div>
		            
		        <div class="card-body">
		            <div class="tab-content">
		                <div id="transfers" class="tab-pane active">
		                    <table class='table table-sm table-bordered table-collapsed table-hovered'>
	                    	    <thead>
	                    	        <tr>
	                    	            <th>#</th>
	                    	            <th>Клиент</th>
	                    	            <th>Наименование</th>
	                    	            <th>Валюта</th>
	                    	            <th>Курс</th>
	                    	            <th>Сумма</th>
	                    	            <th>Сумма руб.</th>
	                    	            <th>Комментарий</th>
	                    	            <th>Журнал</th>
	                    	        </tr>
	                    	    </thead>
	                    	    <tbody>
	                    	        <?php 
	                    	            $transfers = $model->transfers;
	                    	            if(is_array($transfers)){
	                    	            	$comSumUs = $comSumEu = $comSumRu = 0;
	                    	                foreach ($transfers as $k=>$t) {
	                    	                	
	                    	                	$comSumUs += $t['currency'] == Currency::C_DOLLAR ? $t['sum'] : 0;
	                    	                	$comSumEu += $t['currency'] == Currency::C_EURO ? $t['sum'] : 0;
	                    	                	$comSumRu += $t['sum_ru'];
	                    	                    ?>
	                    	                    <tr>
	                        	                    <td><?php echo Html::encode($k+1);?></td>
	                                            	<td>
	                                            		<?php echo Html::encode($t['client_id'] ? $t->client->name : "не указан");?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t['name']);?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode(Currency::getCurrencyTitle($t['currency']));?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t['course']);?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t['sum']);?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t['sum_ru']);?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t['comment']);?>
	                                            	</td>
	                                            	<td style="text-align: center;">
	                                            		<?php echo Html::a("Журнал",['transferspackage/transfer-story','id'=>$t['id']],['class'=>'btnTransferStory'])?>
	                                            	</td>
	                    	                    </tr>
	                    	                    <?php
	                    	                }
	                    	                ?>

	                    	                <tr style="font-weight: bold;">
	                    	                	<td colspan="5">Итого</td>
	                    	                	<td><?php 
	                    	                			echo $comSumUs." ".Currency::getCurrencyTitle(Currency::C_DOLLAR)." - ",$comSumEu." ".Currency::getCurrencyTitle(Currency::C_EURO);
	                    	                		?>
	                    	                	</td>
	                    	                	<td><?php echo $comSumRu;?></td>
	                    	                	<td colspan="2"></td>
	                    	                </tr>
	                    	                <?php
	                    	            }
	                    	        
	                    	        ?>
	                    	    </tbody>
	                    	</table>
		                </div>
		                    
		                    
		                <div id="expenses" class="tab-pane fade in">
		                    <table class='table table-sm table-bordered table-collapsed table-hovered'>
	                    	    <thead>  
	                    	        <tr>
	                    	            <th>#</th>
	                    	            <th>Дата</th>
	                    	            <th>Поставщик</th>
	                    	            <th>Валюта</th>
	                    	            <th>Курс</th>
	                    	            <th>Сумма</th>
	                    	            <th>Сумма Руб</th>
	                    	            <th>Комментарий</th>
	                    	            <th>Журнал</th>
	                    	        </tr>
	                    	    </thead>
	                    	    <tbody>
	                    	        <?php 
	                    	            $expenses= $model->SellerExpenses;
	                    	            if(is_array($expenses)){
	                    	            	$comSumUs = $comSumEu = $comSumRu = 0;
	                    	                foreach ($expenses as $k=>$t) {
	                    	                		$comSumUs += $t['currency'] == Currency::C_DOLLAR ? $t['sum'] : 0;
	                    	                		$comSumEu += $t['currency'] == Currency::C_EURO ? $t['sum'] : 0;
	                    	                		$comSumRu += $t['sum_ru'];

	                    	                    ?>
	                    	                    <tr>
	                        	                    <td><?php echo Html::encode($k+1);?></td>
	                                            	<td>
	                                            		<?php echo date("d.m.Y",strtotime($t->date));?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t->seller_id? $t->seller->name : "не указан");?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode(Currency::getCurrencyTitle($t->currency));?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t->course);?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t->sum);?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t->sum_ru);?>
	                                            	</td>
	                                            	<td>
	                                            		<?php echo Html::encode($t->comment);?>
	                                            	</td>
	                                            	<td style="text-align: center;">
	                                            		<?php echo Html::a("Журнал",['transferspackage/expenses-story','id'=>$t->id],['class'=>'btnExpensesStory'])?>
	                                            	</td>
	                    	                    </tr>
	                    	                    <?php
	                    	                }
	                    	                ?>

	                    	                <tr style="font-weight: bold;">
	                    	                	<td colspan="5">Итого</td>
	                    	                	<td><?php 
	                    	                			echo $comSumUs." ".Currency::getCurrencyTitle(Currency::C_DOLLAR)." - ",$comSumEu." ".Currency::getCurrencyTitle(Currency::C_EURO);
	                    	                		?>
	                    	                	</td>
	                    	                	<td><?php echo $comSumRu;?></td>
	                    	                	<td colspan="2"></td>
	                    	                </tr>
	                    	                <?php
	                    	            }
	                    	        
	                    	        ?>
	                    	        </tbody>
	                    	</table>
		                </div>
		            </div>
		        </div>
	        </div>
	    </div>
	</div>
</div>
<?php 

$script = <<<JS
		$(function(){
			$("#btnShowFiles").click(function(event){
				event.preventDefault();
				$("#modalFiles").modal('show').find(".modal-body").load($(this).attr('href'));
			});

			$("#btnChangeStatus").click(function(event){
				event.preventDefault();
				$("#modalChangeStatus").modal('show').find(".modal-body").load($(this).attr('href'));
			});

			$("#btnStoryStatus").click(function(event){
				event.preventDefault();
				$("#modalStoryStatus").modal('show').find(".modal-body").load($(this).attr('href'));
			});

			$(".btnTransferStory").click(function(event){
				event.preventDefault();
				$("#modalTransferStory").modal('show').find(".modal-body").load($(this).attr('href'));
			});

			$(".btnExpensesStory").click(function(event){
				event.preventDefault();
				$("#modalExpensesStory").modal('show').find(".modal-body").load($(this).attr('href'));
			});

		});


JS;


$this->registerJs($script);

	Modal::begin([
		'header'=>"Файлы",
		'id'=>'modalFiles',
		'size'=>'modal-lg'
	]);
	Modal::end();


	Modal::begin([
		'header'=>"Изменение статуса",
		'id'=>'modalChangeStatus',
		'size'=>'modal-lg'
	]);
	Modal::end();

	Modal::begin([
		'header'=>"История статуса",
		'id'=>'modalStoryStatus',
		'size'=>'modal-lg'
	]);
	Modal::end();


	Modal::begin([
		'header'=>"Журнал услуги",
		'id'=>'modalTransferStory',
		'size'=>'modal-all'
	]);
	Modal::end();


	Modal::begin([
		'header'=>"Журнал расхода",
		'id'=>'modalExpensesStory',
		'size'=>'modal-all'
	]);
	Modal::end();
?>