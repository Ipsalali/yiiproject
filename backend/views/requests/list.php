<?php

use yii\helpers\Html;

$this->title = "Список методов в API 1С";

?>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-collapsed table-hovered">
			<thead>
				<tr>
					<th>#</th>
					<th>Метод</th>
					<th><?php echo Html::a("Выполнить все",['requests/exec-all'],['btn btn-success']);?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>1</td>
					<td>Выгрузка контрагента</td>
					<td><?php echo Html::a("Выполнить",['requests/exec-loadcustomer'],['btn btn-primary']);?></td>
				</tr>
				<tr>
					<td>2</td>
					<td>Выгрузка заявки</td>
					<td><?php echo Html::a("Выполнить",['requests/exec-createreceipts'],['btn btn-primary']);?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>