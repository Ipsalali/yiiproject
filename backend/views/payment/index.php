<?php 
use yii\helpers\Html; 


$this->title = "Вид оплат";
?>
 
<?php if(Yii::$app->session->hasFlash('PaymentDeletedError')): ?>
<div class="alert alert-error">
    There was an error deleting your payment!
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('PaymentDeleted')): ?>
<div class="alert alert-success">
    Your payment has successfully been deleted!
</div>
<?php endif; ?>

<div class="container">
<?php echo Html::a('Добавить вид оплаты', array('payment/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>
<hr />
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