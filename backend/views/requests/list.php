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
					<td>Запрос остатков по бригаде</td>
					<td><?php echo Html::a("Выполнить",['requests/exec-unloadremnant'],['btn btn-primary']);?></td>
				</tr>
				<tr>
					<td>2</td>
					<td>Смена пароля пользователя</td>
					<td><?php echo Html::a("Выполнить",['requests/exec-useraccountload'],['btn btn-primary']);?></td>
				</tr>
				<tr>
					<td>3</td>
					<td>Выгрузка рапорта</td>
					<td><?php echo Html::a("Выполнить",['requests/exec-raportload'],['btn btn-primary']);?></td>
				</tr>
				<tr>
					<td>4</td>
					<td>Расчетать кв.м. сделанных работ</td>
					<td><?php echo Html::a("Выполнить",['requests/exec-calcsquare'],['btn btn-primary']);?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>