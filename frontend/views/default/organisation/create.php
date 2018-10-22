<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\helper\ArrayHelper;
use common\models\Organisation;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

?>

<div class="org_save">
<?php $form = ActiveForm::begin(['id' => 'org_data_update']); ?>
    <input type="hidden" name="org_id" value="<?php echo $model->id?>">
    <div class="row">
        <div class="col-xs-7 org_btn_save">
            <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'org-save-button']); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7">
            <?php echo $form->field($model, 'org_name')->textInput(array('class' => 'form-control cash')); ?>
        </div>
        <div class="col-xs-5">
            <?php echo $form->field($model, 'inn')->textInput(array('class' => 'form-control')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7">
            <?php echo $form->field($model, 'org_check')->textInput(array('class' => 'form-control')); ?>
        </div>
        <div class="col-xs-5">
            <?php echo $form->field($model, 'kpp')->textInput(array('class' => 'form-control')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($model, 'org_address')->textInput(array('class' => 'form-control')); ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'headman')->textInput(array('class' => 'form-control')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <?php echo $form->field($model, 'bank_name')->textInput(array('class' => 'form-control')); ?>
        </div>
        
    </div>
    <div class="row">
        <div class="col-xs-7">
            <?php echo $form->field($model, 'bank_check')->textInput(array('class' => 'form-control')); ?>
        </div>
        <div class="col-xs-5">
            <?php echo $form->field($model, 'bik')->textInput(array('class' => 'form-control')); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-5">
            <?php echo $form->field($model, 'description')->textarea(['class'=>'form-control cash']); ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'payment')->radioList(Organisation::$pay_labels); ?>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>

<?php
$sql = <<<sql

    var check_cach_type = function(radio){
            
            if(parseInt(radio.val())){
                $("input").not('.cash').parents(".form-group").not('.field-organisation-payment').hide();
            }else{
                $("input").not('.cash').parents(".form-group").show();
            }
            
        };

    check_cach_type($("input[name=\'Organisation[payment]\']:checked"));

    $("input[name=\'Organisation[payment]\']").change(function(){check_cach_type($(this));});
sql;

$this->registerJs($sql);

?>