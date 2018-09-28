<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use frontend\bootstrap4\GridView;
use common\models\Currency;

$this->title = "Список переводов";

$this->params['breadcrumbs'][]=$this->title;
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
                <div class="btn-group">
                    <?php echo Html::a('Создать', ['transferspackage/form'], ['class' => 'btn btn-primary']); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="card-body">    
    <?php 

        echo GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $modelFilters,
                'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
                'tableOptions'=>['class'=>'table table-sm table-striped table-bordered table-hover as_index'],
                'showFooter'=>true,
                'columns'=>[
                    [
                        'class'=>'yii\grid\SerialColumn'
                    ],
                    [
                        'attribute'=>"date",
                        "value"=>function($c){
                            return Html::a(date("d.m.Y",strtotime($c->date)),["transferspackage/read","id"=>$c->id]);
                        },
                        "format"=>"html",
                        'filter'=>Html::input("date",'TransferspackageFilter[date_from]',$modelFilters->date_from ? date("Y-m-d",strtotime($modelFilters->date_from)) : null,['class'=>'form-control']) . Html::input("date",'TransferspackageFilter[date_to]',$modelFilters->date_to ? date("Y-m-d",strtotime($modelFilters->date_to)) : null,['class'=>'form-control']),
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