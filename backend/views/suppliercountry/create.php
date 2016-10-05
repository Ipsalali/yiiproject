<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

?>

<h2>Справочник <span>"Страна поставки"</span></h2>
<div class="status_create_page">
<?php $form = ActiveForm::begin(['id' => 'country_create']); ?>

    <?php echo $form->field($model, 'country')->textInput(array('class' => 'form-control')); ?>
    <?php echo $form->field($model, 'code')->textInput(array('class' => 'form-control')); ?>
    <div class="form-actions">
        <?php echo Html::submitButton('Сохранить страну',['class' => 'btn btn-primary', 'name' => 'country-create-button']); ?>
    </div>
    
<?php ActiveForm::end(); ?>
</div>