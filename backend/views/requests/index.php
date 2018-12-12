<?php

use yii\helpers\{Html,ArrayHelper,Url};
use backend\widgets\monitoring\Monitoring;

$this->title = "Журнал запросов в 1С";

?>
<div class="row">
	<div class="col-md-12">
		<?php
			echo Html::a("Список методов",['requests/list'],['class'=>'btn btn-primary']);
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php echo Monitoring::widget([
				'options'=>[
					'urlUpdate'=>Url::to(['requests/index']),
					'pageUrl'=>Url::to(['requests/index']),
					'gridView' => $view,
				]
		]);?>
	</div>
</div>