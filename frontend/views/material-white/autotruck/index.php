<?php 

use yii\helpers\Html;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use frontend\bootstrap4\GridView;
use yii\data\ActiveDataProvider;

use frontend\models\Autotruck;
use frontend\modules\PaymentStateFilter;

use common\models\SupplierCountry;
use common\models\Status;
use common\models\PaymentState;

$user = \Yii::$app->user->identity;

$this->title = "Список заявок";
$this->params['breadcrumbs'][]=$this->title;

$statuses = Status::find()->asArray()->all();
$statusesIndexed = ArrayHelper::map($statuses,'id','title');
$countries = $user->countries;
$countriesIndexed = ArrayHelper::map($countries,'id','country');
$PaymentStateFilter = PaymentStateFilter::getFilters();
?>

<div class="card">
	<div class="card-header card-header-primary">
		<div class="row">
			<div class="col-4">
				<h1 class="card-title">Список заявок</h1>
			</div>
			<div class="col-3 offset-5 text-right">
				<?php echo Html::a('Добавить заявку', array('autotruck/form'), array('class' => 'btn btn-primary')); ?>
			</div>
		</div>
	</div>

	<div class="card-body">
		<div class="row">
			<div class="col-12">
			<?php 
			echo GridView::widget([
					'dataProvider'=>$dataProvider,
					'filterModel'=>$autotruckSearch,
					'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
					'options'=>['class'=>'main_autotruck'],
					'tableOptions'=>['class'=>'table table-sm table-striped table-bordered table-hover'],
					'columns'=>[
						[
							'attribute'=>'date',
							'value'=>function (Autotruck $a) {
		                            return Html::a(Html::encode($a->rudate), Url::to(['autotruck/read', 'id' => $a->id]));
		                        },
							'format'=>'raw',
							'filter'=>Html::input("date",'AutotruckSearch[date_from]',$autotruckSearch->date_from ? date("Y-m-d",strtotime($autotruckSearch->date_from)) : null,['class'=>'form-control']) . Html::input("date",'AutotruckSearch[date_to]',$autotruckSearch->date_to ? date("Y-m-d",strtotime($autotruckSearch->date_to)) : null,['class'=>'form-control']),
							'contentOptions'=>['class'=>'td_date'],
							'headerOptions'=>['class'=>'th_date']
						],
						'name',
						[
			                'attribute'=>'auto_number',
			                'value'=>function($c){
			                    return $c->auto_number;
			                },
							'contentOptions'=>['class'=>'td_auto_number'],
							'headerOptions'=>['class'=>'th_auto_number']
		            	],
						[
			                'attribute'=>'auto_name',
			                'value'=>function($c){
			                    return $c->auto_name;
			                },
							'contentOptions'=>['class'=>'td_auto_name'],
							'headerOptions'=>['class'=>'th_auto_name']
		            	],
		            	'decor',
						[
			                'attribute'=>'common_weight',
			                'value'=>function($c){
			                    return $c->common_weight;
			                },
							'contentOptions'=>['class'=>'td_cweigh'],
							'headerOptions'=>['class'=>'th_date']
		            	],
						[
							'attribute'=>"status",
							'value'=>function($m) use($statusesIndexed){
								return array_key_exists($m->status,$statusesIndexed) ? $statusesIndexed[$m->status] : "Не найден";
							},
							'filter'=>Html::activeDropDownList($autotruckSearch,'status',ArrayHelper::map($statuses,'id','title'),['class'=>'form-control','prompt'=>'Выберите статус'])
						    ,
							'contentOptions'=>['class'=>'td_status'],
							'headerOptions'=>['class'=>'th_status']
						],
						[
							'attribute'=>'country',
							'value'=>function($m)use($countriesIndexed){
								return array_key_exists($m->country,$countriesIndexed) ? $countriesIndexed[$m->country] : "Не найден";
							},
							'filter'=>Html::activeDropDownList($autotruckSearch,'country',ArrayHelper::map($countries,'id','country'),['class'=>'form-control','prompt'=>'Выберите cтрану'])
						    ,
						    'contentOptions'=>['class'=>'td_country'],
							'headerOptions'=>['class'=>'th_country']
						],
						[
			                'attribute'=>'Статус реализации',
			                'value'=>function($c){
			                    return null;
			                },
			                'format'=>'raw',
			                'filter'=>Html::activeDropDownList($autotruckSearch,'implements_state',Arrayhelper::map($PaymentStateFilter,'id','title'),['class'=>'form-control','prompt'=>'Статус платежа']),
			                
							'contentOptions'=>['class'=>'td_implements_state'],
							'headerOptions'=>['class'=>'th_implements_state']
		            	]
					]
				])?>
			</div>
		</div>
	</div>
</div>