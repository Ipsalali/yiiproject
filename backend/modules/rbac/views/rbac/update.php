<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model budyaga\users\models\AuthRule */

$this->title = Yii::t('rbac', 'UPDATE_MODEL', ['type' => Yii::t('rbac', $this->context->getModelTypeTitle($type)), 'name' => $model->name]);
$this->params['breadcrumbs'][] = ['label' => 'Роли и права', 'url' => ['rbac/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-rule-update">

    <?php echo $this->render('_formAuthItem', [
        'model' => $model,
        'type' => $type
    ]) ?>

</div>
