<?php

namespace WSUserActions\widgets\jsworker;

use yii\web\AssetBundle;

class JsWorkerAsset extends AssetBundle{

	public $sourcePath = '@WSUserActions/widgets/jsworker/assets/js';
	public $css = [
		//'css/jsworker.css'
	];

	public $js = [
	];

	public $depends = ['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset'];
}

?>