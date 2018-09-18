<?php

use yii\helpers\{Html,Url};


if(isset($model->id)){
?>
	<h2><?= Yii::t('rbac', 'USER_PERMISSIONS')?></h2>
	<p>
	    <?= Yii::$app->user->can('superadmin', ['user' => $model]) ? Html::a(Yii::t('yii', 'Update'), ['/rbac/rbac/permissions', 'id' => $model->id], ['class' => 'btn btn-primary']) : '' ?>
	</p>
<?php
}
$auth = Yii::$app->authManager;

foreach ($permissions as $permission) : ?>
<div class="list-group">
    <div class="list-group-item list-group-item-info">
        <h4 class="list-group-item-heading"><?= $permission->description?></h4>
        <?php
        $children = $auth->getChildren($permission->name);
        if (count($children)) {
            echo $this->context->render('_branch', ['children' => $children]);
        }
        ?>
    </div>
</div>
<?php endforeach;?>