<?php

namespace backend\widgets\monitoring;

use yii\web\AssetBundle;

class MonitoringAsset extends AssetBundle{

	public $sourcePath = '@backend/widgets/monitoring/assets';
	public $css = ['css/monitoring.css'];

	public $js = ['js/monitoring.js'];

	public $depends = ['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset'];
}

?>