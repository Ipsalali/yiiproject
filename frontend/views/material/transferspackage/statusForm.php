<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
?>

<div class="row">
	<div class="col-12">
	<?php $form = ActiveForm::begin(['id'=>'changeStatus']);?>
		<?php 
			echo $form->field($model,'status')->dropDownList(ArrayHelper::map($model->getStatuses(),'id','title'),['prompt'=>'Статус']);
		?>
		<?php 
			echo $form->field($model,'status_date')->widget(\yii\jui\DatePicker::classname(),['language' => 'ru','dateFormat'=>'dd-MM-yyyy',"options"=>array("class"=>"form-control")]); 
		?>
		<?php
			echo Html::submitButton("Изменить",['class'=>"btn btn-success"])
		?>			
	<?php ActiveForm::end();?>
	</div>
</div>