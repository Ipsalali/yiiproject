<?php use yii\helpers\Html; ?>
<div class="pull-right btn-group">
    <?php echo Html::a('Изменить', array('suppliercountrн/update', 'id' => $model->id), array('class' => 'btn btn-primary')); ?>
    <?php echo Html::a('Удалить', array('suppliercountry/delete', 'id' => $model->id), array('class' => 'btn btn-danger','data-confirm'=>'Подтвердите удаление!')); ?>
</div>
 
<h1>Страна: <?php echo $model->country; ?></h1>
<h3>Код страны: <?php echo $model->code; ?></h3>