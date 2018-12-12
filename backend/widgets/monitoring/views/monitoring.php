<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

?>
<div class="row" style="margin-top: 20px;">
	<?php 
		if($data['showTitle']){
			$class="col-xs-offset-8";
	?>
	<div class="col-xs-3">
		<h2 style="margin-top: 0px;"><?php echo $data['title']?></h2>
	</div>
	<?php 
		}else{
			$class="col-xs-offset-11";
		} 
	?>
	<div class="col-xs-1 <?php echo $class?>">
		<select id="monitoring_options" class="form-control">
			<option value="0">Не обновлять</option>
			<option value="10000">10 сек</option>
			<option value="15000">15 сек</option>
			<option value="30000">30 сек</option>
		</select>
	</div>
</div>
<div class="row">
	<div class="col-xs-12" style="text-align: right;">
		<ul class="monitor_manager">
			<li><a id="toggle_filtres" data-status="1">Фильтры</a></li>
			<li><a href="<?php echo $data['urlUpdate'];?>" id="btn_refresh">Обновить</a></li>
			<li>Последнее обновление <span id="last_update_time"><?php echo date("d.m.Y H:i",time());?></span></li>
		</ul>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<div id="monitor_target" class="monitor">
			<?php echo $data['gridView']; ?>
		</div>
	</div>
</div>

<?php
$script = <<<JS

		MC.interval = 10000;
		MC.pageUrl = "{$data['pageUrl']}";
JS;

$this->registerJs($script);
?>