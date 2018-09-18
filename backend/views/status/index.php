<?php 
use yii\helpers\Html; 


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

<?php echo Html::a('Добавить статус заявки', array('status/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>
<hr />
<table class="table table-striped table-hover">
    <tr>
        <td>#</td>
        <td>Заголовок</td>
        <td>Описание</td>
        <td>Порядок</td>
        <td>Отправлять счет?</td>
        <td>Управление</td>
    </tr>
    <?php foreach ($data as $status): ?>
        <tr>
            <td>
                <?php echo Html::a($status->id, array('status/read', 'id'=>$status->id)); ?>
            </td>
            <td><?php echo Html::a($status->title, array('status/read', 'id'=>$status->id)); ?></td>
            <td><?php echo $status->description; ?></td>
            <td><?php echo $status->sort; ?></td>
            <td><?php echo $status->send_check ? "Да": "Нет"; ?></td>
            <td>
                <?php echo Html::a("Редактировать", array('status/update', 'id'=>$status->id), array('class'=>'icon icon-edit')); ?>
                <?php echo Html::a("Удалить", array('status/delete', 'id'=>$status->id), array('class'=>'icon icon-trash remove_check')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>