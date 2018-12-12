<?php

use yii\helpers\Html;

$this->title = "Журнал запросов в 1С";

?>
<div class="row">
	<div class="col-md-12">
		<?php
			echo Html::a("Список методов",['requests/list'],['class'=>'btn btn-primary']);
		?>
	</div>
</div>