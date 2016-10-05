<?php use yii\helpers\Html; ?>
<div class="pull-right btn-group">
    <?php echo Html::a('Изменить', array('suppliercountrн/update', 'id' => $status->id), array('class' => 'btn btn-primary')); ?>
    <?php echo Html::a('Удалить', array('suppliercountrн', 'id' => $status->id), array('class' => 'btn btn-danger')); ?>
</div>
 
<h1>Страна: <?php echo $model->country; ?></h1>
<h3>Код страны: <?php echo $model->code; ?></h3>