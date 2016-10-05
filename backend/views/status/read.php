<?php use yii\helpers\Html; ?>

<div class="container">
<div class="pull-right btn-group">
    <?php echo Html::a('Update', array('status/update', 'id' => $status->id), array('class' => 'btn btn-primary')); ?>
    <?php echo Html::a('Delete', array('status/delete', 'id' => $status->id), array('class' => 'btn btn-danger')); ?>
</div>
 
<?php echo Html::a('Cтатус заявок', array('status/index'), array('class' => '')); ?>

<h1>Заголовок: <?php echo $status->title; ?></h1>
<p>Описание: <?php echo $status->description; ?></p>
<p>Прядок: <?php echo $status->sort; ?></p>
<div>Шаблон письма: <div><?php echo $status->notification_template; ?></div></div>
</div>
