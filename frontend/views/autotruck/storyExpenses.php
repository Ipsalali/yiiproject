<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\bootstrap\ActiveForm;



?>

<?php 

	$stories = $model == null ? null : $model->getHistory();
	if($stories == null || !count($stories)){
?>
	<div class="row">
		<div class="col-xs-12">
			<div class="alert alert-warning">
				Журнал пуст!
			</div>
		</div>
	</div>
<?php
	}else{

?>
		<div class="row">
			<div class="col-xs-12">
				<table class="table table-bordered table-hovered table-collapsed">
					<tr>
								<th>№</th>
								<th><?php echo $model->getAttributeLabel("date")?></th>
								<th><?php echo $model->getAttributeLabel("organisation")?></th>
								<th><?php echo $model->getAttributeLabel("manager_id")?></th>
								<th><?php echo $model->getAttributeLabel("cost")?></th>
								<th><?php echo $model->getAttributeLabel("comment")?></th>

								<th>Версия</th>
								<th>Действие</th>
								<th>Автор действия</th>
								<th>Время действия</th>
							</tr>
							<?php
								foreach ($stories as $key => $s) {
									?>
									<tr>
										<td><?php echo ++$key?></td>
										<td><?php echo $s['date'];?></td>
										<td><?php echo $s['organisation']?></td>
										<td><?php echo $s['manager_id']?></td>
										<td><?php echo $s['cost']?></td>
										<td><?php echo $s['comment']?></td>

										<td><?php echo $s['version']?></td>
										<td>
											<?php 
												$type = $s['type_action'] == 1 && $s['version'] > 1 ? 2 : $s['type_action'];
												
												echo $model->getStoryAction($type);
											?>
										</td>
										<td><?php echo $s['creator_name']," #",$s['creator_id']?></td>
										<td><?php echo $s['created_at']?></td>
									</tr>
									<?php
								}
							?>
				</table>
			</div>
		</div>
		<?php
	}
?>
