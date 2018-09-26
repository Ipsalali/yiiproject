<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ResetPasswordForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Восстановление пароля';
?>

<div class="row">
    <div class="col-md-4 offset-md-4 align-self-center">
        <div class="login-card card card-default">
            <div class="card-header">
                <h3 class="card-title"><?php echo $this->title; ?></h3>
            </div>
            <div class="card-body">
                <p class="card-title">Пожалуйста введите новый пароль:</p>
                <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                <?php echo $form->field($model, 'password')->passwordInput() ?>

                <div class="form-group">
                    <?php echo Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>