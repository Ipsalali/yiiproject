<?php

use yii\helpers\Html;
use yii\helpers\Url;

use yii\bootstrap\ActiveForm;
use common\models\Currency;


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
				<table class="table table-sm table-bordered table-hovered table-collapsed">
					<tr>
								<th>№</th>
								<th><?php echo $model->getAttributeLabel("date")?></th>
								<th><?php echo $model->getAttributeLabel("seller_id")?></th>
								<th><?php echo $model->getAttributeLabel("currency")?></th>
								<th><?php echo $model->getAttributeLabel("course")?></th>
								<th><?php echo $model->getAttributeLabel("sum")?></th>
								<th><?php echo $model->getAttributeLabel("sum_ru")?></th>
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
										<td><?php echo $s['seller_name']?></td>
										<td><?php echo Currency::getCurrencyTitle($s['currency']);?></td>
										<td><?php echo $s['course']?></td>
										<td><?php echo $s['sum']?></td>
										<td><?php echo $s['sum_ru']?></td>
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
