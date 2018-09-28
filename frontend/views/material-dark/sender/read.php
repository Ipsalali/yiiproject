<?php 
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label'=>"Список отправителей",'url'=>Url::to(['sender/index'])];
$this->params['breadcrumbs'][]=$this->title;
?>

<div class="card">
	<div class="card-header card-header-primary">
		<div class="row">
			<div class="col">
				<h2 class="card-title"><?php echo $this->title?></h2>
			</div>
			<div class="col text-right">
				<div class="btn-group">
					<?php echo Html::a('Изменить', ['sender/update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
		    		<?php echo Html::a('Удалить', ['sender/delete', 'id' => $model->id], ['class' => 'btn btn-danger','data-confirm'=>'Подтвердите удаление!']); ?>
				</div>
				
			</div>
		</div>
	</div>
	<div class="card-body">
		
		<div class="row">
			<div class="col-6">
				<p>Телефон: <?php echo $model->phone; ?></p>
				<p>E-mail: <?php echo $model->email; ?></p>
			</div>
		</div>
		
	</div>
</div>

