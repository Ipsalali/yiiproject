<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use backend\models\User;
use yii\helpers\Url;


$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;





echo Html::a("Добавить менеджера",array("site/userform"),array("class"=>"btn btn-success"));

//echo Html::a("Запустить процедуру перерасчета сверки",array("site/sverka-restart"),array("class"=>"btn btn-primary","data-confirm"=>"Предупреждение, выполнение скрипта займет несколько минут!"));

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel'=>$filterModel,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'id',
        'username',
        'name',
        'email:email',
        'phone',

        ['class' => 'yii\grid\ActionColumn',
         'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{permit}&nbsp;&nbsp;{delete}',
         'buttons' =>
             [
                 'view' => function ($url, $model) {
                     return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/site/user', 'id' => $model->id]), [
                         'title' => Yii::t('yii', 'view')
                     ]); },
                 'update' => function ($url, $model) {
                     return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['/site/userform', 'id' => $model->id]), [
                         'title' => Yii::t('yii', 'update')
                     ]); },   
                 'permit' => function ($url, $model) {
                     return Html::a('<span class="glyphicon glyphicon-wrench"></span>', Url::to(['/permit/user/view', 'id' => $model->id]), [
                         'title' => Yii::t('yii', 'Change user role')
                     ]); },
             ]
        ],
    ],
]);

?>