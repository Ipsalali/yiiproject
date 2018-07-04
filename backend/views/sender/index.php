<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

 $this->title = "Отправитель";
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
<?php $form = ActiveForm::begin(['id' => 'country_create','action'=>Url::to(['sender/create'])]); ?>

    <?php echo $form->field($model, 'name')->textInput(); ?>
    <?php echo $form->field($model, 'phone')->textInput(); ?>
    <?php echo $form->field($model, 'email')->textInput(); ?>
    <div class="form-actions">
        <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'country-create-button']); ?>
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
        <td>Телефон</td>
        <td>E-mail</td>
        <td>Управление</td>
    </tr>
    <?php foreach ($data as $type): ?>
        <tr>
            <td>
                <?php echo Html::a($type->id, array('sender/read', 'id'=>$type->id)); ?>
            </td>
            <td><?php echo $type->name; ?></td>
            <td><?php echo $type->phone; ?></td>
            <td><?php echo $type->email; ?></td>
            <td>
                <?php echo Html::a("Редактировать", array('sender/update', 'id'=>$type->id), array('class'=>'icon icon-edit')); ?>
                <?php echo Html::a("Удалить", array('sender/delete', 'id'=>$type->id), array('class'=>'icon icon-trash remove_check')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>