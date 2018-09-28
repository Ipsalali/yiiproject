<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use frontend\bootstrap4\GridView;

$this->title = "Список отправителей";
$this->params['breadcrumbs'][]=$this->title;
?>

<div class="card">
    <?php $form = ActiveForm::begin(['id' => 'country_create','action'=>Url::to(['sender/create'])]); ?>
    <div class="card-header card-header-primary">
        <div class="row">
                <div class="col">
                    <h3 class="card-title">Новый отправитель:</h3>
                </div>
                <div class="col text-right">
                    <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'country-create-button']); ?>
                </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-3">
                <?php echo $form->field($model, 'name')->textInput(); ?>
            </div>
            <div class="col-3">
                <?php echo $form->field($model, 'phone')->textInput(); ?>
            </div>
            <div class="col-3">
                <?php echo $form->field($model, 'email')->textInput(); ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?> 
</div>


<div class="card">
    <div class="card-header card-header-primary">
        <h3 class="card-title">Список отправителей</h3>
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
                        'class'=>'yii\grid\SerialColumn',
                        //'footer'=>'Итого'
                    ],
                    [
                        'attribute'=>"name",
                        "value"=>function($c){
                            return Html::a($c->name, Url::to(["sender/read","id"=> $c->id]));
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
                                 return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/sender/update', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Редактировать')
                                 ]); },   
                             'delete' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/sender/delete', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Удалить'),'data-confirm'=>'Подтвердите свои действия'
                                 ]); },
                         ]
                    ]
                ]
            ]);

    ?>
</div>