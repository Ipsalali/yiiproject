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
								<th><?php echo $model->getAttributeLabel("client")?></th>
								<th><?php echo $model->getAttributeLabel("weight")?></th>
								<th><?php echo $model->getAttributeLabel("rate")?></th>
								<th><?php echo $model->getAttributeLabel("summa_us")?></th>
								<th><?php echo $model->getAttributeLabel("comment")?></th>
								<th><?php echo $model->getAttributeLabel("info")?></th>
								<th><?php echo $model->getAttributeLabel("type")?></th>
								<!-- <th><?php echo $model->getAttributeLabel("out_sock")?></th> -->
								<th><?php echo $model->getAttributeLabel("sender")?></th>
								<th><?php echo $model->getAttributeLabel("count_place")?></th>
								<th><?php echo $model->getAttributeLabel("package")?></th>

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
										<td><?php echo $s['client'];?></td>
										<td><?php echo $s['weight']?></td>
										<td><?php echo $s['rate']?></td>
										<td><?php echo $s['summa_us']?></td>
										<td><?php echo $s['comment']?></td>
										<td><?php echo $s['info']?></td>
										<td><?php echo $s['type'] == 1 ? "Услуга" : null;?></td>
										<!-- <td><?php echo $s['out_sock']?></td> -->
										<td><?php echo $s['sender']?></td>
										<td><?php echo $s['count_place']?></td>
										<td><?php echo $s['package']?></td>

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
