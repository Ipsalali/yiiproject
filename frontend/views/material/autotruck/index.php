<?php 

use yii\helpers\Html;
use yii\helpers\Url;

use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;

use frontend\models\Autotruck;
use frontend\modules\PaymentStateFilter;

use common\models\SupplierCountry;
use common\models\Status;
use common\models\PaymentState;


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
	echo GridView::widget([
			'dataProvider'=>$dataProvider,
			'filterModel'=>$autotruckSearch,
			'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
			'options'=>['class'=>'main_autotruck'],
			'tableOptions'=>['class'=>'table table-striped table-bordered table-hover as_index'],
			'columns'=>[
				// ['class'=>'yii\grid\SerialColumn'],
				[
					'attribute'=>'date',
					'value'=>function (Autotruck $a) {
                            return Html::a(Html::encode($a->rudate), Url::to(['autotruck/read', 'id' => $a->id]));
                        },
					'format'=>'raw',
					'filter'=>"C:".Html::input("date",'AutotruckSearch[date_from]',$autotruckSearch->date_from ? date("Y-m-d",strtotime($autotruckSearch->date_from)) : null) . " По:".Html::input("date",'AutotruckSearch[date_to]',$autotruckSearch->date_to ? date("Y-m-d",strtotime($autotruckSearch->date_to)) : null),
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
				// ['class'=>'yii\grid\ActionColumn']
			]
		])?>
		</div>
	   </div>
	</div>
</div>
<div class="clearfix"></div>