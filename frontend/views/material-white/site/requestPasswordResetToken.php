<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Запрос на восстановление пароля';
?>
<div class="row">
    <div class="col-md-8 offset-md-2 align-self-center">
        <div class="login-card card card-default">
            <div class="card-header">
                <h1 class="card-title"><?php echo Html::encode($this->title) ?></h1>
            </div>
            <div class="card-body">
            <p class="card-title">Пожалуйста введите поле E-mail. Ссылка для восстановления пароля будет отправлена вам на почту.</p>

            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?php echo $form->field($model, 'email') ?>

                <div class="form-group">
                    <?php echo Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
