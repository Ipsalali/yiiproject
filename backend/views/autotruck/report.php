<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use frontend\models\Autotruck;
use yii\helpers\Url;
use common\models\SupplierCountry;
use common\models\Status;

?>

<div class="autotrucks">
	<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h2>Отчет</h2>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
	<?php 

	echo GridView::widget([
			'dataProvider'=>$dataProvider,
			'summary'=>'',
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
			'columns'=>[
				['class'=>'yii\grid\SerialColumn'],
				[
					'attribute'=>'date',
					'label'=>'Дата',
					'value'=>function($a){
						return Html::a(Html::encode(date("d.m.Y",strtotime($a['date']))), Url::to(['autotruck/read', 'id' => $a['id']]));
					},
					'format'=>'html',
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
				'country:text:Страна',
				'weight:decimal:Вес',
				'sum_us:decimal:Сумма $',
				'sum_ru:decimal:Сумма Руб.',
				'expenses:decimal:Расход $'
			]
		])
		?>
		</div>
	   </div>
	</div>
</div>
<div class="clearfix"></div>