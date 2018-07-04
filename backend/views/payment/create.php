<?php 
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>

<div class="row status_create_page">
<?php $form = ActiveForm::begin(['id' => 'payment_create']); ?>
    <div class="col-xs-3">
        <?php echo $form->field($model, 'title')->textInput(); ?>
    </div>
    <div class="col-xs-3">
        <?php echo $form->field($model, 'code')->textInput(); ?>
    </div>
    <div class="col-xs-3">
        <?php echo Html::submitButton('Сохранить',['class' => 'btn btn-primary pull-right', 'name' => 'payment-create-button']); ?>
    </div>
    
<?php ActiveForm::end(); ?>
</div>

<div class="row">
    <div class="col-xs-6">
    <table class="table table-striped table-hover">
        <tr>
            <td>#</td>
            <td>Заголовок</td>
            <td>Код</td>
            <td>Управление</td>
        </tr>
        <?php foreach ($data as $model): ?>
            <tr>
                <td>
                    <?php echo $model->id; ?>
                </td>
                <td><?php echo $model->title; ?></td>
                <td><?php echo $model->code; ?></td>
                <td>
                    <?php echo Html::a("Редактировать", array('payment/create', 'id'=>$model->id), array('class'=>'icon icon-edit')); ?>
                    <?php echo Html::a("Удалить", array('payment/delete', 'id'=>$model->id), array('class'=>'icon icon-trash remove_check')); ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    </div>
</div>