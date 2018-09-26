<?php 
use yii\helpers\Html; 


$this->title = "Поставщик: ". $model->name;
?>

<div class="row">
	<div class="col-xs-4">
		<h2><?php echo $this->title?></h2>
	</div>
	<div class="col-xs-8">
		
		<div class="pull-right btn-group" style="margin-top: 20px;">
		    	<?php echo Html::a('Изменить', array('sellers/update', 'id' => $model->id), array('class' => 'btn btn-primary')); ?>
		    	<?php echo Html::a('Удалить', array('sellers/delete', 'id' => $model->id), array('class' => 'btn btn-danger','data-confirm'=>'Подтвердите удаление!')); ?>
		</div>
		
		
	</div>
</div>


<div class="row">
	<div class="col-xs-6">
		<p>Логин: <?php echo $model->username; ?></p>
		<p>Название: <?php echo $model->name; ?></p>
		<p>Телефон: <?php echo $model->phone; ?></p>
		<p>E-mail: <?php echo $model->email; ?></p>
	</div>
</div>
