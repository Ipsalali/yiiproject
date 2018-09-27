<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use frontend\bootstrap4\GridView;
use yii\data\ActiveDataProvider;
use frontend\models\Autotruck;
use frontend\modules\AutotruckReport;
use common\models\SupplierCountry;
use common\models\Status;

$this->title = "Отчет";
$this->params['breadcrumbs'][]=$this->title;

$user = \Yii::$app->user->identity;
?>

<div class="card">
	<div class="card-header card-header-primary">
		<h1 class="card-title">Отчет</h1>
	</div>
	<div class="card-body">
	<?php 
		echo GridView::widget([
			'dataProvider'=>$dataProvider,
			'filterModel'=>$autotruckReport,
			'summary'=>'',
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
			'showFooter'=>true,
			'tableOptions'=>['class'=>'table table-sm table-striped table-bordered table-hover ali_table'],
			'columns'=>[
				['class'=>'yii\grid\SerialColumn','footer'=>'Итого'],
				[
					'attribute'=>'date',
					'label'=>'Дата',
					'value'=>function($a){
						return Html::a(Html::encode(date("d.m.Y",strtotime($a['date']))), Url::to(['autotruck/read', 'id' => $a['id']]));
					},
					'format'=>'raw',
					'filter'=>Html::input("date",'AutotruckReport[date_from]',$autotruckReport->date_from ? date("Y-m-d",strtotime($autotruckReport->date_from)) : null,['class'=>'form-control']) . Html::input("date",'AutotruckReport[date_to]',$autotruckReport->date_to ? date("Y-m-d",strtotime($autotruckReport->date_to)) : null,['class'=>'form-control']),

					'contentOptions'=>['class'=>'td_date'],
					   'headerOptions'=>['class'=>'th_date']
				],
				[
					'attribute'=>'name',
					'label'=>'Инвойс',
					'value'=>function($a){
						return Html::a(Html::encode($a['name']), Url::to(['autotruck/read', 'id' => $a['id']]));
					},
					'format'=>'html',
				],
				'course:decimal:Курс',
				[
					'attribute'=>'country',
					'label'=>'Страна',
					'value'=>function($a){
						return $a['country'];
					},
					'filter'=>Html::activeDropDownList($autotruckReport,'country',ArrayHelper::map($user->countries,'id','country'),['class'=>'form-control','prompt'=>'Выберите cтрану'])
				    
				],
				[
					'attribute'=>'weight',
					'label'=>'Вес',
					'value'=>function($a){
						$w = round($a['weight'],2);
						AutotruckReport::$common_weight += $w;
						return $w;
					},
					'footerOptions'=>['class'=>'ft_common_weight','id'=>'common_weight']
				],
				[
					'attribute'=>'sum_us',
					'label'=>'Сумма $',
					'value'=>function($a){
						$w = round($a['sum_us'],2);

						AutotruckReport::$common_sum_us += $w;
						return $w;
					},
					'footerOptions'=>['class'=>'ft_common_sum_us','id'=>'common_sum_us']
				],
				[
					'attribute'=>'sum_ru',
					'label'=>'Сумма Руб',
					'value'=>function($a){
						$w = round($a['sum_ru'],2);
						AutotruckReport::$common_sum_ru += $w;
						return $w;
					},
					'footerOptions'=>['class'=>'ft_common_sum_ru','id'=>'common_sum_ru']
				],
				[
					'attribute'=>'expenses',
					'label'=>'Расход $',
					'value'=>function($a){
						$e = round($a['expenses'],2);
						AutotruckReport::$common_expenses_ru += $e;
						return $e;
					},
					'footerOptions'=>['class'=>'ft_common_expenses','id'=>'common_expenses']
				],
				//'weight:decimal:Вес',
				//'sum_us:decimal:Сумма $',
				//'sum_ru:decimal:Сумма Руб.',
				//'expenses:decimal:Расход $',
				[
					'attribute'=>'common',
					'label'=>'Итого $',
					'value'=>function($a){
						$w = round($a['sum_us']-$a['expenses'],2);

						AutotruckReport::$total_common += $w;
						return $w;
					},
					'footerOptions'=>['class'=>'ft_common_weight','id'=>'total_common']
				]
			]
		])
		?>

		<script type="text/javascript">
			$(function(){

				$("#common_weight").text(<?php echo AutotruckReport::$common_weight;?>);
				$("#common_sum_us").text(<?php echo AutotruckReport::$common_sum_us;?>);
				$("#common_sum_ru").text(<?php echo AutotruckReport::$common_sum_ru;?>);
				$("#common_expenses").text(<?php echo AutotruckReport::$common_expenses_ru;?>);
				$("#total_common").text(<?php echo AutotruckReport::$total_common;?>);

			});
		</script>
	</div>
</div>