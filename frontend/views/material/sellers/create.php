<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = "Поставщики";
?>

<div class="row">
	<div class="col-xs-12">
		<h2><?php echo $this->title?></h2>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
	<?php $form = ActiveForm::begin(['id' => 'seller_create']); ?>
	
    	<div class="row">
    		<div class="col-xs-3">
    			<?php echo $form->field($model, 'username')->textInput(); ?>
    		</div>
    		<div class="col-xs-3">
    			<?php echo $form->field($model, 'name')->textInput(); ?>
    		</div>
    	</div>
    	
    	<div class="row">
    		<div class="col-xs-3">
    			<?php echo $form->field($model, 'phone')->textInput(); ?>
    		</div>
    		<div class="col-xs-3">
    			<?php echo $form->field($model, 'email')->textInput(); ?>
    		</div>
    	</div>
    	<div class="row">
    		<div class="col-xs-3">
    			<?php echo $form->field($model, 'password')->textInput(["value"=>Yii::$app->getSecurity()->generateRandomString(6)]); ?>
    		</div>
    		<div class="col-xs-3">
    			<?php 
    			    if(isset($model->id) && $model->id){
    			        echo Html::label("Поменять пароль",['id'=>"change_password"]);
    			        echo Html::checkbox("change_password",0);
    			    }
    			?>
    		</div>
    	</div>

        <div class="form-actions">
            <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'country-create-button']); ?>
        </div>
    
	<?php ActiveForm::end(); ?>
	</div>
</div>

