<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model budyaga\users\models\AuthRule */

$this->title = Yii::t('rbac', 'CREATE_MODEL', ['type' => $this->context->getModelTypeTitle($type)]);
$this->params['breadcrumbs'][] = ['label' => 'Роли и права', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-create">
    <?= $this->render('_form' . $this->context->getModelName($type), [
        'model' => $model,
        'type' => $type
    ]) ?>
</div>
