<?php

use yii\helpers\Html;

$this->title = "Проверка email адресов";
?>
<div class="row">
	<div class="col-xs-12">
		<table class="table table-bordered table-sm table-hovered table-collapsed">
			<thead>
				<tr>
					<th>#</th>
					<th>Клиент</th>
					<th>Email</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				
			<?php
				if(isset($emails) && is_array($emails)){
					foreach ($emails as $key => $item) {
				?>
				<tr>
					<td><?php echo ++$key;?></td>
					<td><?php echo $item['full_name'];?></td>
					<td><?php echo $item['email'];?></td>
					<td><?php ?></td>
				</tr>
				<?php
					}
				}
			?>

			</tbody>
		</table>
	</div>
</div>