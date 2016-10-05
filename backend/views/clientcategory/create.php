<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\ClientCategory;
use yii\helpers\ArrayHelper;

$category_list = ($model->cc_id) ? ClientCategory::find()->where('cc_id <> '.$model->cc_id)->all() :ClientCategory::find()->all();

?>
<?php echo Html::a('Категория клиентов', array('clientcategory/index'), array('class' => '')); ?>

<div class="status_create_page">
<?php $form = ActiveForm::begin(['id' => 'status_create']); ?>

    <?php echo $form->field($model, 'cc_title')->textInput(array('class' => 'form-control')); ?>
    
    <?php
    	echo $form->field($model, 'cc_description')->textarea(array("class"=>'form-control'));
    ?>
    
    <div>
        <span>Родительская категория:</span>
        <?php echo Html::activeDropDownList($model,'cc_parent',ArrayHelper::map($category_list,'cc_id','cc_title'),
        ['prompt'=>'Выберите статус']);?>
    </div>
    
    <div class="form-actions">
        <?php echo Html::submitButton('Сохранить категорию',['class' => 'btn btn-primary', 'name' => 'status-create-button']); ?>
    </div>
    
<?php ActiveForm::end(); ?>
</div>