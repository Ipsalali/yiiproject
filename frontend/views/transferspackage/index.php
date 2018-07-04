<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\date\DatePicker;

$this->title = "Переводы";
?>
<div class="row">
	<div class="col-xs-4">
		<h3>
			<?php echo $this->title?>
		</h3>
	</div>
	<div class="col-xs-8">
		<div class="pull-right btn-group" style="margin-top: 20px;">
		    <?php echo Html::a('Создать', array('transferspackage/create'), array('class' => 'btn btn-primary')); ?>
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

        echo \yii\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $modelFilters,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-striped table-bordered table-hover as_index'],
                'showFooter'=>true,
                'columns'=>[
                    [
                        'class'=>'yii\grid\SerialColumn',
                        //'footer'=>'Итого'
                    ],
                    [
                        'attribute'=>"date",
                        "value"=>function($c){
                            return Html::a(date("d.m.Y",strtotime($c->date)),["transferspackage/read","id"=>$c->id]);
                        },
                        "format"=>"html",
                        'filter'=>DatePicker::widget([
							'model'=>$modelFilters,
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
                    ],
                    [
                        'attribute'=>"name",
                        "value"=>function($c){
                            return Html::a($c->name, Url::to(["transferspackage/read","id"=> $c->id]));
                        },
                        "format"=>"html"
                    ],
                    [
                        'attribute'=>"status",
                        "value"=>function($c){
                            return $c->statusTitle;
                        },
                        "format"=>"html",
                        "filter"=>Html::activeDropDownList($modelFilters,'status',ArrayHelper::map($modelFilters->getStatuses(),'id','title'),['class'=>'form-control','prompt'=>'Выберите статус'])
                    ],
                    [
                        'attribute'=>"currency",
                        "value"=>function($c){
                            return $c->currencyTitle;
                        },
                        "format"=>"html",
                        "filter"=>Html::activeDropDownList($modelFilters,'currency',ArrayHelper::map($modelFilters->getCurrencies(),'id','title'),['class'=>'form-control','prompt'=>'Выберите валюту'])
                    ],
                    [
                        'attribute'=>"course",
                        "value"=>function($c){
                            return $c->course;
                        },
                        "format"=>"html"
                    ],
                    [
                        'attribute'=>"comment",
                        "value"=>function($c){
                            return $c->comment;
                        },
                        "format"=>"html"
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{update}&nbsp;&nbsp;{delete}',
                        'buttons' =>
                         [
                             
                             'update' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/transferspackage/create', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Редактировать')
                                 ]); },   
                             'delete' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/transferspackage/delete', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Удалить'),'data-confirm'=>'Подтвердите свои действия'
                                 ]); },
                         ]
                    ]
                ]
            ]);

        ?>
    </div>
</div>