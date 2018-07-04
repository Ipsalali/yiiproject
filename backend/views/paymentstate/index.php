<?php 
use yii\helpers\Html; 
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

 $this->title = "Статус оплаты";
?>
 
<?php if(Yii::$app->session->hasFlash('PaymentStateDeleteError')): ?>
<div class="alert alert-error">
    Ошибка не удалось удалить статус оплаты.
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('PaymentStateDeleted')): ?>
<div class="alert alert-success">
    Статус оплаты удален успешно.
</div>
<?php endif; ?>

<div class="status_create_page">
<div class="row">
<?php $form = ActiveForm::begin(['id' => 'paymentsate_create','action'=>Url::to(['paymentstate/create','id'=>$model->id])]); ?>
    <div class="col-xs-2">
        <?php echo $form->field($model, 'title')->textInput(array('class' => 'form-control')); ?>
    </div>
    <div class="col-xs-2">
        <?php echo $form->field($model, 'color')->input('text',array('class' => 'form-control colorPicker')); ?>
    </div>
    <div class="col-xs-2">
        <?php echo $form->field($model, 'default_value')->checkbox(array('value'=>'1')); ?>
    </div>
    <div class="col-xs-2">
        <?php echo $form->field($model, 'end_state')->checkbox(array('value'=>'1')); ?>
    </div>
    <div class="col-xs-2">
        <?php echo $form->field($model, 'sum_state')->checkbox(array('value'=>'1')); ?>
    </div>
    <div class="col-xs-2" style="padding-top:25px; ">
        <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary', 'name' => 'country-create-button']); ?>
    </div>
    
<?php ActiveForm::end(); ?>
</div>
</div>
<?php //echo Html::a('Добавить страну', array('suppliercountry/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>
<hr />
<table class="table table-striped table-hover">
    <tr>
        <td colspan="2">Код</td>
        <td colspan="4">Название</td>
        <td>Управление</td>
    </tr>
    <?php foreach ($list as $state): ?>
        <tr>
            <td>
                <?php echo Html::encode($state->id); ?>
            </td>
            <td style="background-color: <?php echo $state->color?>"></td>
            <td><?php echo Html::encode($state->title); ?></td>
            <td><?php echo Html::encode($state->default_value? "По умолчанию":''); ?></td>
            <td><?php echo Html::encode($state->end_state? "Конечное состояние":''); ?></td>
            <td><?php echo Html::encode($state->sum_state? "Промежуточное состояние":''); ?></td>
            <td>
                <?php echo Html::a("Редактировать", array('paymentstate/create', 'id'=>$state->id), array('class'=>'icon icon-edit')); ?>
                <?php echo Html::a("Удалить", array('paymentstate/delete', 'id'=>$state->id), array('class'=>'icon icon-trash remove_check')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>