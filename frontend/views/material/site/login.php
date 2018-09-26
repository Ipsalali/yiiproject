<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход';
?>

<div class="row">
    <div class="col-md-4 offset-md-4 align-self-center">
        <div class="login-card card card-default">
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->title; ?></h3>
            </div>
            <div class="card-body">
                <p class="card-title">Пожалуйста введите ваши регистрационные данные для входа в систему:</p>
                <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                    <?php echo $form->field($model, 'email')->label("E-mail");?>

                    <?php echo $form->field($model, 'password')->passwordInput()->label("Пароль"); ?>

                    <?php echo $form->field($model, 'rememberMe')->checkbox()->label("Запомнить меня"); ?>

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
