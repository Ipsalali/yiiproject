<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\models\Autotruck;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\TypePackaging;
use common\models\Status;
use common\models\SupplierCountry;
use frontend\bootstrap4\Modal;

$this->title = $client->name;
$this->params['breadcrumbs'][] = ['label'=>"Список клиентов",'url'=>Url::to(['client/index'])];
$this->params['breadcrumbs'][]=$this->title;

$packages = TypePackaging::find()->all();
$clientCategory = $client->category;
$clientUser = $client->user;
$clientManager = $client->managerUser;
$autotruckStatuses = Status::getIndexedArray();
$autotruckCountries = SupplierCountry::getIndexedArray();

?>


<div class="row">
	<div class="col-7">
		<div class="card">
			<div class="card-header card-header-primary">
				<div class="row">
					<div class="col">
						<h3><?php echo Html::encode($client->name); ?>&nbsp(<span><?php echo Html::encode($clientCategory->cc_title); ?></span>)</h3>
					</div>
					<div class="col text-right">
						<div class="btn-group">
							<?php echo Html::a("Журнал редактирования клиента",['client/client-story','id'=>$client->id],['id'=>'btnClientStory','class'=>'btn btn-success'])?>
						    <?php echo Html::a('Редактировать', array('client/update', 'id' => $client->id), array('class' => 'btn btn-primary')); ?>
						    <?php echo Html::a('Удалить', array('client/delete', 'id' => $client->id), array('class' => 'btn btn-danger remove_check')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
			 		<div class="col-7">
			 			<div class="row">
							<div class="col-6">
								<h4>Название</h4>
								<p>
									<?php echo Html::encode($client->full_name); ?>
								</p>
							</div>
							<div class="col-3">
								<h4>Email</h4>
								<p><?php echo Html::encode(isset($clientUser->email) && $clientUser->email ? $clientUser->email :"E-mail не указан"); ?></p>
							</div>
							<div class="col-3">
								<h4>Телефон</h4>
								<p><?php echo Html::encode(($client->phone)?$client->phone:"Телефон не указан"); ?></p>
							</div>
						</div>
						<div class="row">
							<div class="col-4">
								<h4>Ответственный:</h4>
								<p><?php echo Html::encode(($clientManager)? $clientManager->name: "Не закреплен к менеджеру."); ?></p>
							</div>
							<div class="col-4">
								<h4>Договор:</h4>
								<p><?php echo Html::encode($client->contract_number); ?></p>
							</div>
							<div class="col-4">
								<h4>Оплата по безналу:</h4>
								<p><?php echo Html::encode($client->payment_clearing); ?> %</p>
							</div>
						</div>
						<div class="row">
							<div class="col-8">
								<h4>Описание:</h4>
								<p><?php echo Html::encode(($client->description)?$client->description:"Нет описания"); ?></p>
							</div>
							<div class="col-4">
								<h4>Организация:</h4>
								<p><?php echo Html::encode(($client->organisation_pay_id) ? $client->organisation->org_name:"Не привязан."); ?></p>
							</div>
						</div>

						<div class="row">
							<div class="col">
								<p>Задолженность: <?php echo $client->user->getManagerSverka();?> $</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-5">
		<div class="card card-chart">
			<div class="card-header card-header-primary">
				<?php if(count($grafik)){?>
					<div class='ct-chart' id="dailySalesChart" style="padding-top: "></div>
				<?php } ?>
			</div>
			<div class="card-body">
				<h4 class="card-title">Количество веса по месяцам</h4>
                <!-- <p class="card-category">Last Campaign Performance</p> -->
			</div>
		</div>
	</div>
</div>
	
<div class="card card-plain">
	<div class="card-header card-header-primary">
		<h4 class="card-title">Заявки клиента</h4>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-12">
				<?php if($autotrucks){?>
					<?php  foreach ($autotrucks as $key => $autotruck){ ?>
				<div id="autotruck_tab_<?=$key?>" class="card">
				  	<div class="card-header">
				  		<div class="row">
					  		<div class="col-9">
					  			<h4>
					  				<?php echo Html::a($autotruck->name." №".$autotruck->id,array('autotruck/read','id'=>$autotruck->id))?>
					  					&nbsp&nbsp&nbsp
					  				<?php echo html::a("Редактировать",array("autotruck/form","id"=>$autotruck->id))?>
					  			</h4>
					  		</div>
					  		<div class="col-3 text-right" >
					  			<span><?php echo date("d.m.Y",strtotime($autotruck->date))?></span><br>
					  				<?php echo Html::a("Скачать счет", array("client/check","client"=>$client->id,"autotruck"=>$autotruck->id),array("class"=>"check btn btn-success"))?>
					  		</div>
				  		</div>
				  	</div>
					<div class="card-body">
						<div class="row">
							<div class="col-4">
								<p><strong>Курс:</strong> <span><?php echo $autotruck->course; ?> руб.</span></p>
								<p><strong>Страна поставки:</strong><span><?php echo array_key_exists($autotruck->country, $autotruckCountries) ? $autotruckCountries[$autotruck->country]['country'] : "" ;?></span></p>
                                <p><strong>Номер машины:</strong> <?php echo $autotruck->auto_number?></p>
								<p><strong>Описание:</strong> <?php echo Html::encode($autotruck->description); ?></p>
                                <p><strong>ГТД:</strong> <?php echo $autotruck->gtd?></p>
							</div>
							<div class="col-3">
								<h4>Статус:</h4>
								<ul>
									<?php
										$story = $autotruck->traceStory;
										if(is_array($story)){
											foreach ($story as $s) { 
												$active_s = ($s->status_id == $autotruck->status)? "active_status" :'';

												if(!array_key_exists($s->status_id, $autotruckStatuses)) continue;
											?>
											<li class="app_status <?php echo $active_s?>">
												<?php echo $autotruckStatuses[$s->status_id]['title']?>
												<span><?php echo date('d.m.Y',strtotime($s->trace_date))?></span>
											</li>
										<?php }
										}
									?>
								</ul>
							</div>
							<div class="col-3">
								<p>Итого кол-во мест: <?php echo $autotruck->totalCountPlace;?></p>
								<?php
									$autotuckPackages = $autotruck->packagesCountPlace;
									if(is_array($packages) && is_array($autotuckPackages)){
										foreach ($packages as $key => $package) {
											$count = array_key_exists($package->id, $autotuckPackages) ? $autotuckPackages[$package->id]['count'] : 0;
											if($count > 0){
											?>
												<p><?php echo $package->title?>: <?php echo $count; ?></p>
											<?php
											}
										}
									}

									if(array_key_exists("none", $autotuckPackages) && $autotuckPackages['none']['count']){
								?>
										<p>Не известная упаковка: <?php echo $autotuckPackages['none']['count']; ?></p>
								<?php } ?>
							</div>
						</div>
						
						<div class="row">
							<div class="col-12">
								<table class="table table-sm table-striped table-hover table-bordered">
									<thead>
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
									</thead>
									<tbody>
									<?php $cweight=0;$crate=0; $total = 0; $total_us = 0;
									foreach ($autotruck->appsCollection as $i => $app) { ?>
											<tr>
												<td>
													<?php echo $i+1?>
													<?php
														$cl = $app['out_stock'] ? 'ok' : '';
													?>
													<span class="app_out_stock <?php echo $cl?>"></span>
												</td>
												<td><?php echo $client->name;?></td>
												
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
										}?>
										<tr>
											<td colspan="3"><strong>Итого</strong></td>
											<td colspan="3"><strong><?php echo $autotruck->totalCountPlace; //$autotruck->getAppCountPlace($client->id)?></strong></td>
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
	<?php } ?>
			</div>
		</div>
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
		'header'=>"Журнал редактирования клиента",
		'id'=>'modalJournal',
		'size'=>'modal-all'
	]);
	Modal::end();
?>
	
		
<script id="source" language="javascript" type="text/javascript">
	$(function () {

			var grafik = [];
			<?php foreach ($grafik as $key => $v) { ?>
				grafik.push(['<?php echo date('M Y',strtotime($key));?>',<?php echo $v['weight'];?>]);
			<?php }?>

			var dataDailySalesChart = {
			        labels: [],
			        series: []
			};
    		if(grafik.length){

				//Графики из шаблона
	    		// var dataDailySalesChart = {
			    //     labels: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
			    //     series: [
			    //       [12, 17, 7, 17, 23, 18, 38]
			    //     ]
			    // };

			    var series = [];
			    var labels = [];
			    grafik.forEach(function(item){
			    	labels.push(item[0]);
			    	series.push(item[1]);
			    });

			    dataDailySalesChart.labels = labels;
			    dataDailySalesChart.series = [series];

				// $("#placeholder").bind("plotclick", function (event, pos, item) {
		  //       	if (item) {
		  //           	$("#grafik_info").html(" Вес " + item.series.data[item.dataIndex][1] + "кг <br>"+"Период " + item.series.data[item.dataIndex][0]);
		  //           	plot.highlight(item.series, item.datapoint);
		  //       	}
		  //   	});
    		}

    		var optionsDailySalesChart = {
			        lineSmooth: Chartist.Interpolation.cardinal({
			          tension: 0
			        }),
			        //low: 0,
			        //high: 50, // creative tim: we recommend you to set the high sa the biggest value + something for a better look
			        chartPadding: {
			          top: 15,
			          right: 0,
			          bottom: 0,
			          left: 0
			        },
			}

	      	var dailySalesChart = new Chartist.Line('#dailySalesChart', dataDailySalesChart, optionsDailySalesChart);
				
			md.startAnimationForLineChart(dailySalesChart);
    		
		});
</script>


