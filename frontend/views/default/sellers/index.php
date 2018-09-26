<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\grid\GridView;

$this->title = "Поставщики";
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-xs-2">
        <h2><?php echo $this->title?></h2>
    </div>
    <div class="col-xs-2 col-xs-offset-8">
        <?php echo Html::a("Создать поставщика",['sellers/create'],['class'=>'btn btn-primary']);?>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <?php 

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
                        'attribute'=>"name",
                        "value"=>function($c){
                            return Html::a($c->name, Url::to(["sellers/read","id"=> $c->id]));
                        },
                        "format"=>"html"
                    ],
                    [
                        'attribute'=>"email",
                        'value'=>"email",
                    ],
                    [
                        'attribute'=>"phone",
                        'value'=>"phone",
                    ],
                  
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{update}&nbsp;&nbsp;{delete}',
                        'buttons' =>
                         [
                             
                             'update' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/sellers/update', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Редактировать')
                                 ]); },   
                             'delete' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/sellers/delete', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Удалить'),'data-confirm'=>'Подтвердите свои действия'
                                 ]); },
                         ]
                    ]
                ]
            ]);

        ?>
    </div>
</div>