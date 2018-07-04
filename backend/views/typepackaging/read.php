<?php use yii\helpers\Html; ?>
<div class="pull-right btn-group">
    <?php echo Html::a('Изменить', array('typepackaging/update', 'id' => $model->id), array('class' => 'btn btn-primary')); ?>
    <?php echo Html::a('Удалить', array('typepackaging/delete', 'id' => $model->id), array('class' => 'btn btn-danger','data-confirm'=>'Подтвердите удаление!')); ?>
</div>
 
<h1>Страна: <?php echo $model->title; ?></h1>