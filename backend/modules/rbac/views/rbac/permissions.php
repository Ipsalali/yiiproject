<?php

use yii\helpers\Html;
use backend\modules\rbac\RbacAsset;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model budyaga\users\models\User */

$this->title = $modelForm->model->user->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rbac', 'Пользователи'), 'url' => ['/site/list']];
$this->params['breadcrumbs'][] = ['label' => $modelForm->model->user->name, 'url' => ['/site/user', 'id' => $modelForm->model->user->id]];
$this->params['breadcrumbs'][] = Yii::t('rbac', 'Роли и права');

$assets = RbacAsset::register($this);
?>
<div class="user-rules">
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-xs-5">
            <?= $form->field($modelForm, 'assigned')->dropDownList(
                ArrayHelper::map(
                    $modelForm->model->assignedRules,
                    function ($data) {
                        return serialize([$data->name, $data->type]);
                    },
                    'description'
                ), ['multiple' => 'multiple', 'size' => '20', 'class' => 'col-xs-12'])->label("Доступно")?>
        </div>
        <div class="col-xs-2 text-center">
            <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="assign"><span class="glyphicon glyphicon-arrow-left"></span></button>
            <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="revoke"><span class="glyphicon glyphicon-arrow-right"></span></button>
        </div>
        <div class="col-xs-5">
            <?= $form->field($modelForm, 'unassigned')->dropDownList(
                ArrayHelper::map(
                    $modelForm->model->notAssignedRules,
                    function ($data) {
                        return serialize([$data->name, $data->type]);
                    },
                    'description'
                ), ['multiple' => 'multiple', 'size' => '20', 'class' => 'col-xs-12'])->label("Не доступно")?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
