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
		<div class="col-12">
			<div class="alert alert-warning">
				Журнал пуст!
			</div>
		</div>
	</div>
<?php
	}else{

?>
		<div class="row">
			<div class="col-12">
				<table class="table table-sm table-bordered">
					<thead>
						<tr>
							<th>№</th>
								<th><?php echo $model->getAttributeLabel("name")?></th>
								<th><?php echo $model->getAttributeLabel("date")?></th>
								<th><?php echo $model->getAttributeLabel("description")?></th>
								<th><?php echo $model->getAttributeLabel("status")?></th>
								<th><?php echo $model->getAttributeLabel("course")?></th>
								<th><?php echo $model->getAttributeLabel("country")?></th>
								<th><?php echo $model->getAttributeLabel("auto_number")?></th>
								<th><?php echo $model->getAttributeLabel("auto_name")?></th>
								<th><?php echo $model->getAttributeLabel("gtd")?></th>
								<th><?php echo $model->getAttributeLabel("decor")?></th>

								<th>Версия</th>
								<th>Действие</th>
								<th>Автор действия</th>
								<th>Время действия</th>
							</tr>
					</thead>
					<tbody>
							<?php
								foreach ($stories as $key => $s) {
									?>
									<tr>
										<td><?php echo ++$key?></td>
										<td><?php echo $s['name'];?></td>
										<td><?php echo $s['date']?></td>
										<td><?php echo $s['description']?></td>
										<td><?php echo $s['status_title']?></td>
										<td><?php echo $s['course']?></td>
										<td><?php echo $s['country_title']?></td>
										<td><?php echo $s['auto_number']?></td>
										<td><?php echo $s['auto_name']?></td>
										<td><?php echo $s['gtd']?></td>
										<td><?php echo $s['decor']?></td>

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
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
?>
