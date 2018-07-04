<?php

use common\helper\Html;
use yii\bootstrap\ActiveForm;
use common\models\SupplierCountry;
use yii\helpers\ArrayHelper;

?>
<div class="container">
<?php $form = ActiveForm::begin(['id'=>"userform"]); ?>
<div class="row">
	<div class="col-xs-4">
		<h3>Данные менеджера</h3>
		<?php echo $form->field($user,'username')->textInput(array('class' => 'form-control'))->label("Логин"); ?>
		<?php echo $form->field($user, 'email')->textInput(array('class' => 'form-control'))->label("E-mail"); ?>
		<?php echo $form->field($user,'name')->textInput(array('class' => 'form-control'))->label("ФИО"); ?>
		<?php echo $form->field($user, 'phone')->textInput(array('class' => 'form-control'))->label("Телефон"); ?>

		
		<?php if($user->id){ ?>
				<?php echo Html::checkbox("resetPass",null,['id'=>'resetPass']); ?>
				<label for="resetPass">Поменять пароль ?</label>
				<?php

				$scripts = <<<JS

				$("#resetPass").change(function(){
					if($(this).prop("checked")){
						$("#user-password").show();
					}else{
						$("#user-password").hide();
					}
				})
JS;

				$this->registerJs($scripts);
			}
		?>
		<?php echo $form->field($user, 'password')->textInput(
            array("value"=>Yii::$app->getSecurity()->generateRandomString(6),'class'=>'form-control','style'=>$user->id ? 'display:none;': ''))->label($user->id ? '': 'Пароль'); ?>

        <div class="form-actions">
        <?php echo Html::submitButton('Submit',['class' => 'btn btn-primary', 'name' => 'post-create-button']); ?>
    	</div>
	</div>

	<div class="col-xs-4">
		<h3></h3>
		<div class="form-group">
		<label class="control-label">Города постащиков</label>
		<?php 
			$selected = [];
			
			foreach ($user->getCountries() as $key => $c) {
				$selected[$c->id]['selected'] = true;
			}
			echo Html::listBox("manager_country",null,ArrayHelper::map(SupplierCountry::find()->all(),'id','country'),['multiple'=>true,'size'=>20,'class'=>'form-control','options'=>$selected])
		?>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>

</div>