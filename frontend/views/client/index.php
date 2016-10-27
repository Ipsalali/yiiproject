<?php
use yii\helpers\Html; 
use yii\widgets\ActiveForm;
use common\models\ClientCategory;
use common\models\User;
use common\models\PaymentState;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Arrayhelper;
use yii\helpers\Url;



$client_cats = ClientCategory::find()->asArray()->all();
$managers = User::getManagers();

?>
 
<?php if(Yii::$app->session->hasFlash('ClientDeletedError')): ?>
<div class="alert alert-error">
    There was an error deleting your post!
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('ClientDeleted')): ?>
<div class="alert alert-success">
    Your post has successfully been deleted!
</div>
<?php endif; ?>

<?php echo Html::a('Добавить клиента', array('client/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>

<?php 

echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $clientSearch,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'columns'=>[
            ['class'=>'yii\grid\SerialColumn'],
            [
                'attribute'=>"name",
                "value"=>function($c){
                    return Html::a($c->name, Url::to(["client/read","id"=> $c->id]));
                },
                "format"=>"html"
            ],
            'email:email',
            'phone',
            [
                'attribute'=>"client_category_id",
                'value'=>"category.cc_title",
                'filter'=>Html::activeDropDownList($clientSearch,'client_category_id',Arrayhelper::map($client_cats,'cc_id','cc_title'),['class'=>'form-control','prompt'=>'Выберите категорию'])
            ],
            [
                'attribute'=>'ipay',
                'value'=>function($c){
                    $p = $c->getIpay();
                    return "<span style='color:".$p->color."'>".$p->title."<span>";
                },
                'format'=>'raw',
                'filter'=>Html::activeDropDownList($clientSearch,'ipay',Arrayhelper::map(PaymentState::find()->where("`default` = '1' OR `end_state` = '1'")->asArray()->all(),'id','title'),['class'=>'form-control','prompt'=>'Статус платежа'])
            ],
            [
                'attribute'=>"manager",
                'value'=>"managerUser.name",
                'filter'=>Html::activeDropDownList($clientSearch,'manager',Arrayhelper::map($managers,'id','name'),['class'=>'form-control','prompt'=>'Выберите менеджера'])
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update}&nbsp;&nbsp;{delete}',
                'buttons' =>
                 [
                     
                     'update' => function ($url, $model) {
                         return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/client/update', 'id' => $model->id]), [
                             'title' => Yii::t('yii', 'update')
                         ]); },   
                     'delete' => function ($url, $model) {
                         return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/client/delete', 'id' => $model->id]), [
                             'title' => Yii::t('yii', 'Change user role')
                         ]); },
                 ]
            ]
        ]
]);

?>

