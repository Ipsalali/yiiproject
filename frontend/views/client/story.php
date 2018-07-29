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
								<th><?php echo $model->getAttributeLabel("name")?></th>
								<th><?php echo $model->getAttributeLabel("full_name")?></th>
								<th><?php echo $model->getAttributeLabel("description")?></th>
								<th><?php echo $model->getAttributeLabel("phone")?></th>
								<th><?php echo $model->getAttributeLabel("email")?></th>
								<th><?php echo $model->getAttributeLabel("user_id")?></th>
								<th><?php echo $model->getAttributeLabel("client_category_id")?></th>
								<th><?php echo $model->getAttributeLabel("manager")?></th>
								<th><?php echo $model->getAttributeLabel("payment_clearing")?></th>
								<th><?php echo $model->getAttributeLabel("organisation_pay_id")?></th>

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
										<td><?php echo $s['name'];?></td>
										<td><?php echo $s['full_name']?></td>
										<td><?php echo $s['description']?></td>
										<td><?php echo $s['phone']?></td>
										<td><?php echo $s['email']?></td>
										<td><?php echo "#".$s['user_id']?></td>
										<td><?php echo $s['client_category_title']?></td>
										<td><?php echo $s['manager_name']?></td>
										<td><?php echo $s['payment_clearing']?></td>
										<td><?php echo $s['org_name']?></td>

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
