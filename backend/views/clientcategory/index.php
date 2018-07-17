<?php 
use yii\helpers\Html; 
use common\models\ClientCategory;

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

<?php echo Html::a('Добавить категорию клиента', array('clientcategory/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>
<hr />
<table class="table table-striped table-hover">
    <tr>
        <td>#</td>
        <td>Родительская категория</td>
        <td>Категория</td>
        <td>Описание</td>
        <td>Управление</td>
    </tr>
    <?php foreach ($data as $client): ?>
        <tr>
            <td>
                <?php echo Html::a($client->cc_id, array('clientcategory/read', 'id'=>$client->cc_id)); ?>
            </td>
            <td><?php echo $client->cc_parent ? $client->parent->cc_title : ""; ?></td>
            <td><?php echo Html::a($client->cc_title, array('clientcategory/read', 'id'=>$client->cc_id)); ?></td>
            <td><?php echo $client->cc_description; ?></td>
            <td>
                <?php echo Html::a("Редактировать", array('clientcategory/update', 'id'=>$client->cc_id), array('class'=>'icon icon-edit')); ?>
                <?php echo Html::a("Удалить", array('clientcategory/delete', 'id'=>$client->cc_id), array('class'=>'icon icon-trash remove_check')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>