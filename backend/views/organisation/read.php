<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Organisation;

$this->title = "Организация";
?>
 
<?php if(Yii::$app->session->hasFlash('StatusDeletedError')): ?>
<div class="alert alert-error">
    There was an error deleting your post!
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('StatusDeleted')): ?>
<div class="alert alert-success">
    Your post has successfully been deleted!
</div>


<?php endif; ?>
<div class="org_page">
<?php if(!($org instanceof Organisation)){ ?>
	<?php echo Html::a('Добавить организацию', array('organisation/create'), array('class' => 'btn btn-primary')); ?>
<?php }else{ ?>
<div class="clearfix"></div>
<div class="row">
        <div class="col-xs-7 org_btn_save">
        	<?php 
        		$form = ActiveForm::begin(['id'=>'submit_to_change_org','action'=>['organisation/create']]);
             	echo Html::submitButton('Изменить',['class' => 'btn btn-primary', 'name' => 'org-save-button']);?>
             	<input type="hidden" name="org_id" value="<?php echo $org->id;?>">
             	<?php ActiveForm::end();?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7">
            <h5>Наименование организации</h5>
            <p><?php echo html::encode($org->org_name);?></p>
        </div>
        <div class="col-xs-5">
            <h5>ИНН</h5>
            <p><?php echo html::encode($org->inn);?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7">
            <h5>Счет организации</h5>
            <p><?php echo html::encode($org->org_check);?></p>
        </div>
        <div class="col-xs-5">
            <h5>КПП</h5>
            <p><?php echo html::encode($org->kpp);?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h5>Руководитель</h5>
            <p><?php echo html::encode($org->headman);?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <h5>Наименование банка</h5>
            <p><?php echo html::encode($org->bank_name);?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-7">
            <h5>Счет банка</h5>
            <p><?php echo html::encode($org->bank_check);?></p>
        </div>
        <div class="col-xs-5">
            <h5>БИК</h5>
            <p><?php echo html::encode($org->bik);?></p>
        </div>
    </div>
<?php } ?>
</div>