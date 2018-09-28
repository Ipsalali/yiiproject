<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\bootstrap\ActiveForm;



?>

<?php 

	$story = $model == null ? null : $model->getStatusSory();
	if($story == null || !count($story)){
?>
	<div class="row">
		<div class="col-12">
			<div class="alert alert-warning">
				История не обнаружена
			</div>
		</div>
	</div>
<?php
	}else{

?>
		<div class="row">
			<div class="col-12">
				<p>Действующий статус: <?php echo $model->getStatusTitle();?>
					<span>
						(<?php echo date("d.m.Y",strtotime($model['status_date']));?>)
					</span>
				</p>
				<ul>
				<?php
					foreach ($story as $key => $s) {
							?>
							<li>
								<?php 
									echo $model->getStatusTitle($s['status']);
								?>
								<span>
									(<?php echo date("d.m.Y",strtotime($s['status_date']));?>)
								</span>
							</li>
							<?php
					}
				?>
				</ul>
			</div>
		</div>
		<?php
	}
?>
