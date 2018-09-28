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
use frontend\modules\ClientSearch;

$this->title = "Список клиентов";
$this->params['breadcrumbs'][]=$this->title;

$client_cats = ClientCategory::find()->asArray()->all();
$managers = User::getManagers();

?>

<div class="card">
    <div class="card-header card-header-primary">
        <div class="row">
            <div class="col-4">
                <h1 class="card-title">Список клиентов</h1>
            </div>
            <div class="col-2 offset-6 text-right">
                <?php echo Html::a('Добавить клиента', ['client/create'], ['class' => 'btn btn-primary']); ?>
            </div>
        </div>
    </div>

    <div class="card-body">
        
    
<?php 

echo \yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $clientSearch,
        'formatter' => ['class' => 'yii\i18n\Formatter','nullDisplay' => ''],
        'tableOptions'=>['class'=>'table table-sm table-striped table-bordered table-hover'],
        'showFooter'=>true,
        'columns'=>[
            [
                'class'=>'yii\grid\SerialColumn',
                'footer'=>'Итого'
            ],
            [
                'attribute'=>"name",
                "value"=>function($c){
                    return Html::a($c['name'], Url::to(["client/read","id"=> $c['id']]));
                },
                "format"=>"html"
            ],
            'user_email:text:E-mail',
            [
                'attribute'=>"phone",
                'value'=>"phone",
                'contentOptions'=>['class'=>'client_phone_column']
            ],
            [
                'attribute'=>"client_category_id",
                'value'=>"category_title",
                'filter'=>Html::activeDropDownList($clientSearch,'client_category_id',Arrayhelper::map($client_cats,'cc_id','cc_title'),['class'=>'form-control','prompt'=>'Выберите категорию'])
            ],
            [
                "format"=>'raw',
                'label'=>'Сверка',
                'value'=>function($m){
                    if($m['user_id']){
                        $s = sprintf("%.2f", $m['sverka_sum']);
                        $class = $s <= 0 ? "green" : "red";

                        ClientSearch::$total_sverka+=$s;
                        return "<span style='color:{$class}'>".$s." $</span>";
                    }
                },
                'footerOptions'=>['class'=>'ft_total_sverka','id'=>'ft_total_sverka']
            ],
            [
                'attribute'=>"manager",
                'value'=>"manager_name",
                'filter'=>Html::activeDropDownList($clientSearch,'manager',Arrayhelper::map($managers,'id','name'),['class'=>'form-control','prompt'=>'Выберите менеджера'])
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' =>
                 [
                     
                    'update' => function ($url, $model) {
                         return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/client/update', 'id' => $model['id']]), [
                             'title' => Yii::t('yii', 'Редактировать')
                         ]); },   
                    'delete' => function ($url, $model) {
                         return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/client/delete', 'id' => $model['id']]), [
                             'title' => Yii::t('yii', 'Удалить'),'data-confirm'=>'Подтвердите свои действия'
                    ]); },
                 ]
            ]
        ]
]);

?>
    <script type="text/javascript">
            $(function(){
                $("#ft_total_sverka").text(<?php echo ClientSearch::$total_sverka;?>+" $");
            });
        </script>
    </div>
</div>