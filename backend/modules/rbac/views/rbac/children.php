<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use backend\modules\rbac\components\PermissionsTreeWidget;
use backend\modules\rbac\RbacAsset;

$this->title = Yii::t('rbac', 'CHILDREN_FOR', ['name' => $modelForm->model->name]);

$this->params['breadcrumbs'][] = ['label' => 'RBAC', 'url' => ['rbac/index']];
$this->params['breadcrumbs'][] = ['label' => $modelForm->model->name, 'url' => ['rbac/update', 'id' => $modelForm->model->name, 'type' => $modelForm->model->type]];
$this->params['breadcrumbs'][] = $this->title;


RbacAsset::register($this);
?>

<div class="row permission-children-editor">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-xs-5 children-list">
        <div class="form-group">
            <input type="text" class="form-control listFilter" placeholder="<?= Yii::t('rbac', 'FILTER_PLACEHOLDER')?>">
        </div>
        <?= $form->field($modelForm, 'assigned')->dropDownList(
            ArrayHelper::map(
                $modelForm->model->children,
                function ($data) {
                    return serialize([$data->name, $data->type]);
                },
                'description'
            ), ['multiple' => 'multiple', 'size' => '20', 'class' => 'col-xs-12'])->label("Доступно");
        ?>
    </div>
    <div class="col-xs-2 text-center">
        <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="assign"><span class="glyphicon glyphicon-arrow-left"></span></button>
        <button class="btn btn-success" type="submit" name="AssignmentForm[action]" value="revoke"><span class="glyphicon glyphicon-arrow-right"></span></button>
    </div>
    <div class="col-xs-5 children-list">
        <div class="form-group">
            <input type="text" class="form-control listFilter" placeholder="<?= Yii::t('rbac', 'FILTER_PLACEHOLDER')?>">
        </div>
        <?= $form->field($modelForm, 'unassigned')->dropDownList(
            ArrayHelper::map($modelForm->model->notChildren,
                function ($data) {
                    return serialize([$data->name, $data->type]);
                },
                'description'
            ), ['multiple' => 'multiple', 'size' => '20', 'class' => 'col-xs-12'])->label("Не доступно");
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<h3><?= Yii::t('rbac', 'CHILDREN')?></h3>
<div class="row">
    <div class="col-xs-12">
        <?= PermissionsTreeWidget::widget(['item' => $modelForm->model])?>
    </div>
</div>