<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use kartik\date\DatePicker;
use kartik\daterange\DateRangePicker;
use backend\models\Autotruck;
use yii\helpers\Url;
use common\models\SupplierCountry;
use common\models\Status;
use common\models\PaymentState;
use backend\modules\PaymentStateFilter;

$this->title = "Заявки";
?>

<div class="autotrucks">
	<div class="row">
		<div class="col-xs-12">
			 <div class="">
			 	<?php echo Html::a('Удалить все', Url::to(['/autotruck/delete-all']), [
		                         'class' => 'btn btn-danger','data-confirm'=>'Предупреждение при удалении заявки, автоматически удалятся все связанные данные!!!Продолжить удаление?'
		                     ]); ?>
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
				['class' => 'yii\grid\ActionColumn',
		         'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{delete}',
		         'buttons' =>
		             [
		                 'view' => function ($url, $model) {
		                     return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/autotruck/read', 'id' => $model->id]), [
		                         'title' => Yii::t('yii', 'view')
		                     ]); },
		                 'update' => function ($url, $model) {
		                     return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/autotruck/update', 'id' => $model->id]), [
		                         'title' => Yii::t('yii', 'update')
		                     ]); },   
		                 'delete' => function ($url, $model) {
		                     return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/autotruck/delete', 'id' => $model->id]), [
		                         'title' => Yii::t('yii', 'Change user role'),'data-confirm'=>'Предупреждение при удалении заявки, автоматически удалятся все связанные данные!!!Продолжить удаление?'
		                     ]); },
		             ]
		        ],
			]
		])?>
		</div>
	</div>
</div>