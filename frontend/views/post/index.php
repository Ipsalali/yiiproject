<?php use yii\helpers\Html; ?>
 
<?php if(Yii::$app->session->hasFlash('PostDeletedError')): ?>
<div class="alert alert-error">
    Не удолось получить id заявки
</div>
<?php endif; ?>
 
<?php if(Yii::$app->session->hasFlash('PostDeleted')): ?>
<div class="alert alert-success">
    Your post has successfully been deleted!
</div>
<?php endif; ?>

<?php echo Html::a('Добавить новость', array('post/create'), array('class' => 'btn btn-primary pull-right')); ?>
<div class="clearfix"></div>
<hr />
<table class="table table-striped table-hover">
    <tr>
        <td>#</td>
        <td>Title</td>
        <td>Created</td>
        <td>Updated</td>
        <td>Creator</td>
        <td>Options</td>
    </tr>
    <?php foreach ($data as $post): ?>
        <tr>
            <td>
                <?php echo Html::a($post->id, array('post/read', 'id'=>$post->id)); ?>
            </td>
            <td><?php echo Html::a($post->title, array('post/read', 'id'=>$post->id)); ?></td>
            <td><?php echo $post->created; ?></td>
            <td><?php echo $post->updated; ?></td>
            <td><?php echo $post->user->username; ?></td>
            <td>
                <?php echo Html::a("Редактировать", array('post/update', 'id'=>$post->id), array('class'=>'icon icon-edit')); ?>
                <?php echo Html::a("Удалить", array('post/delete', 'id'=>$post->id), array('class'=>'icon icon-trash')); ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>