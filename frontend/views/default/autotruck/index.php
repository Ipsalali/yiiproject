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
use common\models\PaymentState;
use common\dictionaries\AutotruckState;
use frontend\modules\PaymentStateFilter;


$user = \Yii::$app->user->identity;

$this->title = "Список заявок";
?>

<div class="autotrucks">
	<div class="main">
	<div class="row">
		<div class="col-xs-6 autotruck_head">
			<div class="autotruck_title">
				<h1>Список заявок</h1>
			</div>
			<div class="new_autotruck">
				<?php echo Html::a('Добавить заявку', array('autotruck/form'), array('class' => 'btn btn-primary')); ?>
			</div>
			
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
	<?php 

	$layout = <<< HTML
    {input1}<br>
    {input2}
    <span class="input-group-addon kv-date-remove">
        <i class="glyphicon glyphicon-remove"></i>
    </span>
HTML;

	echo GridView::widget([
			'dataProvider'=>$dataProvider,
			'filterModel'=>$autotruckSearch,
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
			'options'=>['class'=>'main_autotruck'],
			'tableOptions'=>['class'=>'table table-striped table-bordered table-hover as_index'],
			'rowOptions'=>function($model){
                    $ops = [];

                    $ops['class'] = array_key_exists($model["state"], AutotruckState::$notification) ? AutotruckState::$notification[$model['state']] : "";

                    return $ops;
               },
			'columns'=>[
				// ['class'=>'yii\grid\SerialColumn'],
				[
					'attribute'=>'date',
					'value'=>function (Autotruck $a) {
                            return Html::a(Html::encode($a->rudate), Url::to(['autotruck/read', 'id' => $a->id]));
                        },
					'format'=>'raw',
					'filter'=>DatePicker::widget([
							'model'=>$autotruckSearch,
							'language'=>'ru',
							'attribute'=>'date_from',
							'attribute2'=>'date_to',
							'options' => ['placeholder' => 'Начальная дата'],
							'options2' => ['placeholder' => 'Конечная дата'],
							'type' => DatePicker::TYPE_RANGE,
							'separator'=>" по ",
							'layout'=>$layout,
							'pluginOptions'=>[
								'autoclose'=>true,
								
        						'format' => 'dd.mm.yyyy'
    						]
						]),
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
					'value'=>'activeStatus.title',
					'filter'=>Html::activeDropDownList($autotruckSearch,'status',ArrayHelper::map(Status::find()->asArray()->all(),'id','title'),['class'=>'form-control','prompt'=>'Выберите статус'])
				    ,
					'contentOptions'=>['class'=>'td_status'],
					'headerOptions'=>['class'=>'th_status']
				],
				[
					'attribute'=>'country',
					'value'=>'supplierCountry.country',
					'filter'=>Html::activeDropDownList($autotruckSearch,'country',ArrayHelper::map($user->countries,'id','country'),['class'=>'form-control','prompt'=>'Выберите cтрану'])
				    ,
				    'contentOptions'=>['class'=>'td_country'],
					'headerOptions'=>['class'=>'th_country']
				],
				[
	                'attribute'=>'Статус реализации',
	                'value'=>function($c){
	                    //$p = $c->getIpay();
	                    return "-";//"<span style='color:".$p->color."'>".$p->title."<span>";
	                },
	                'format'=>'raw',
	                'filter'=>Html::activeDropDownList($autotruckSearch,'implements_state',Arrayhelper::map(PaymentStateFilter::getFilters(),'id','title'),['class'=>'form-control','prompt'=>'Статус платежа']),
	                
					'contentOptions'=>['class'=>'td_implements_state'],
					'headerOptions'=>['class'=>'th_implements_state']
            	],
            	[
            		'attribute'=>"Состояние",
            		'value'=>function($c){
            			$v = \common\dictionaries\AutotruckState::getLabels($c['state']);
            			return is_array($v) ? "" : $v;
            		}
            	]
				// ['class'=>'yii\grid\ActionColumn']
			]
		])?>
		</div>
	   </div>
	</div>
</div>
<div class="clearfix"></div>