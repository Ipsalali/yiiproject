<?php use yii\helpers\Html; ?>
<div class="pull-right btn-group">
    <?php echo Html::a('Изменить', array('sender/update', 'id' => $model->id), array('class' => 'btn btn-primary')); ?>
    <?php echo Html::a('Удалить', array('sender/delete', 'id' => $model->id), array('class' => 'btn btn-danger','data-confirm'=>'Подтвердите удаление!')); ?>
</div>
 
<h1>Отправитель: <?php echo $model->name; ?></h1>

<p>Телефон: <?php echo $model->phone; ?></p>
<p>E-mail: <?php echo $model->email; ?></p>