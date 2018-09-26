<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

$this->title = "Отправитель";
?>

<div class="row" style="margin-top: 20px;">
    <div class="col-xs-12">
        

    <?php if(Yii::$app->session->hasFlash('StatusDeletedError')): ?>
    <div class="alert alert-error">
        There was an error deleting your post!
    </div>
    <?php endif; ?>
     
    <?php if(Yii::$app->session->hasFlash('StatusDeleted')): ?>
    <div class="alert alert-success">
        Your post has successfully been deleted!
    </div>
    <?php endif; ?>

    <div class="status_create_page">
    <?php $form = ActiveForm::begin(['id' => 'country_create','action'=>Url::to(['sender/create'])]); ?>
        <div class="row">
            <div class="col-xs-6">
                <h3>Новый отправитель:</h3>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-3">
                <?php echo $form->field($model, 'name')->textInput(); ?>
            </div>
            <div class="col-xs-3">
                <?php echo $form->field($model, 'phone')->textInput(); ?>
            </div>
            <div class="col-xs-3">
                <?php echo $form->field($model, 'email')->textInput(); ?>
            </div>
            <div class="col-xs-3" style="padding-top:25px">
                <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'country-create-button']); ?>
            </div>
            
        </div>
        
    <?php ActiveForm::end(); ?>
    </div>


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
</div>