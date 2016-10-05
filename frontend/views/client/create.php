<?php 
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use common\models\ClientCategory;
?>
 
<?php $form = ActiveForm::begin(['id' => 'client_create']); ?>
    
    <div class="client_form">
        <h3>Данные о клиенте</h3>
        <?php echo $form->field($data['client'], 'full_name')->textInput(array('class' => 'form-control')); ?>
        <?php echo $form->field($data['client'], 'name')->textInput(array('class' => 'form-control')); ?>
        <?php echo $form->field($data['client'], 'description')->textInput(array('class' => 'form-control')); ?>
        <?php echo $form->field($data['client'], 'phone')->textInput(array('class' => 'form-control')); ?>
        <?php echo $form->field($data['client'], 'contract_number')->textInput(array('class' => 'form-control')); ?>
        <?php echo $form->field($data['client'], 'payment_clearing')->textInput(array('class' => 'form-control')); ?>
        <div class="">
        <?php echo $form->field($data['client'], 'client_category_id')->dropDownList(ArrayHelper::map(ClientCategory::find()->all(),'cc_id','cc_title'),['prompt'=>'Выберите категорию']); ?>
        </div>
    </div>

    <?php if($mode === "create"){ ?>
    <div class="client_form">
        <h3>Данные для профиля клиента</h3>
        <?php //echo $form->field($data['user'], 'username')->textInput(array('class' => 'form-control')); ?>
        <?php echo $form->field($data['user'], 'email')->textInput(array('class' => 'form-control')); ?>
        <?=$form->field($data['user'], 'password')->textInput(
            array('readonly' => true,"value"=>"12345qwE")//Yii::$app->getSecurity()->generateRandomString(6)
        ); ?>
    </div>
    <?php }else{ ?> 
        <?php echo $form->field($data['client']->user, 'email')->textInput(array('class' => 'form-control')); ?>
    <?php } ?>

    <?php echo $form->field($data['client'], 'manager')->dropDownList(ArrayHelper::map($managers,'id','username'),['prompt'=>'Выберите менеджера']); ?>

    <div class="form-actions">
        <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'post-create-button']); ?>
    </div>
    
<?php ActiveForm::end(); ?>