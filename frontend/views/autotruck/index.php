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
use frontend\modules\PaymentStateFilter;


?>

<div class="autotrucks">

	<?php if(Yii::$app->session->hasFlash('AutotruckSaved')): ?>
		<div class="alert alert-success">
    		Заявка сохранена!
		</div>
	<?php endif; ?>
	<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h1>Заявки</h1>
			 <div class="new_app">
				<?php echo Html::a('Добавить заявку', array('autotruck/create'), array('class' => 'btn btn-primary')); ?>
			 </div>
		</div>
	</div>
	<div class="row">
		<div class="col-xs-12">
	<?php 

	$layout = <<< HTML
    {input1}
    {separator}
    {input2}
    <span class="input-group-addon kv-date-remove">
        <i class="glyphicon glyphicon-remove"></i>
    </span>
HTML;

	echo GridView::widget([
			'dataProvider'=>$dataProvider,
			'filterModel'=>$autotruckSearch,
			'columns'=>[
				['class'=>'yii\grid\SerialColumn'],
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
						])
				],
				'name',
				[
	                'attribute'=>'Статус реализации',
	                'value'=>function($c){
	                    //$p = $c->getIpay();
	                    return "-";//"<span style='color:".$p->color."'>".$p->title."<span>";
	                },
	                'format'=>'raw',
	                'filter'=>Html::activeDropDownList($autotruckSearch,'implements_state',Arrayhelper::map(PaymentStateFilter::getFilters(),'id','title'),['class'=>'form-control','prompt'=>'Статус платежа'])
            	],
				[
					'attribute'=>"status",
					'value'=>'activeStatus.title',
					'filter'=>Html::activeDropDownList($autotruckSearch,'status',ArrayHelper::map(Status::find()->asArray()->all(),'id','title'),['class'=>'form-control','prompt'=>'Выберите статус'])
				],
				[
					'attribute'=>'country',
					'value'=>'supplierCountry.country',
					'filter'=>Html::activeDropDownList($autotruckSearch,'country',ArrayHelper::map(SupplierCountry::find()->asArray()->all(),'id','country'),['class'=>'form-control','prompt'=>'Выберите cтрану'])
				],
				// ['class'=>'yii\grid\ActionColumn']
			]
		])?>
		</div>
	   </div>
	</div>
</div>
<div class="clearfix"></div>