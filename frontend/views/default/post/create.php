<?php 
use yii\helpers\Html;
use mihaildev\ckeditor\CKEditor;
use yii\bootstrap\ActiveForm;
use mihaildev\elfinder\ElFinder;

?>
 
<?php $form = ActiveForm::begin(['id' => 'post_create']); ?>

    <?php echo $form->field($model, 'title')->textInput(array('class' => 'col-sm-7')); ?>
    <?php
    	echo $form->field($model, 'content')->widget(CKEditor::className(),[
    		'editorOptions' => ElFinder::ckeditorOptions(['elfinder'],[
       		'preset' => 'standard', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
        	'inline' => false, //по умолчанию false
    	       ]),
	]);

    ?>
    <div class="form-actions">
        <?php echo Html::submitButton('Submit',['class' => 'btn btn-primary', 'name' => 'post-create-button']); ?>
    </div>
<?php ActiveForm::end(); ?>