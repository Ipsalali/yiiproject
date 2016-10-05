<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Пожалуйста введите ваши регистрационные данные для входа в систему:</p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                <?= $form->field($model, 'email')->label("E-mail");?>

                <?= $form->field($model, 'password')->passwordInput()->label("Пароль"); ?>

                <?= $form->field($model, 'rememberMe')->checkbox()->label("Запомнить меня"); ?>

                <div style="color:#999;margin:1em 0">
                    Если вы забыли свой пароль вы можете <?= Html::a('восстановить его', ['site/request-password-reset']) ?>.
                </div>

                <div class="form-group">
                    <?= Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
</div>