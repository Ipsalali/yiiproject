<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

 $this->title = "Статус";
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

<div class="status_create_page">
<?php $form = ActiveForm::begin(['id' => 'country_create','action'=>Url::to(['suppliercountry/create'])]); ?>

    <?php echo $form->field($model, 'country')->textInput(array('class' => 'form-control')); ?>
    <?php echo $form->field($model, 'code')->textInput(array('class' => 'form-control')); ?>
    <div class="form-actions">
        <?php echo Html::submitButton('Добавить страну',['class' => 'btn btn-primary', 'name' => 'country-create-button']); ?>
    </div>
    
<?php ActiveForm::end(); ?>
</div>
<?php //echo Html::a('Добавить страну', array('suppliercountry/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>
<hr />
<table class="table table-striped table-hover">
    <tr>
        <td>#</td>
        <td>Название</td>
        <td>Код</td>
        <td>Управление</td>
    </tr>
    <?php foreach ($data as $country): ?>
        <tr>
            <td>
                <?php echo Html::a($country->id, array('suppliercountry/read', 'id'=>$country->id)); ?>
            </td>
            <td><?php echo $country->country; ?></td>
            <td><?php echo $country->code; ?></td>
            <td>
                <?php echo Html::a("Редактировать", array('suppliercountry/update', 'id'=>$country->id), array('class'=>'icon icon-edit')); ?>
                <?php echo Html::a("Удалить", array('suppliercountry/delete', 'id'=>$country->id), array('class'=>'icon icon-trash remove_check')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>