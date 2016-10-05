<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<div class="container">
<div class="userform">
	<?php $form = ActiveForm::begin(['id'=>"userform"]); ?>
		<h3>Данные менеджера</h3>
		<?php echo $form->field($user,'username')->textInput(array('class' => 'form-control'))->label("Логин"); ?>
		<?php echo $form->field($user, 'email')->textInput(array('class' => 'form-control'))->label("E-mail"); ?>
		<?php echo $form->field($user,'name')->textInput(array('class' => 'form-control'))->label("ФИО"); ?>
		<?php echo $form->field($user, 'phone')->textInput(array('class' => 'form-control'))->label("Телефон"); ?>
		<?=$form->field($user, 'password')->textInput(
            array('readonly' => true,"value"=>"12345qwE")//Yii::$app->getSecurity()->generateRandomString(6)
        ); ?>

        <div class="form-actions">
        <?php echo Html::submitButton('Submit',['class' => 'btn btn-primary', 'name' => 'post-create-button']); ?>
    </div>
	<?php ActiveForm::end(); ?>
</div>
</div>