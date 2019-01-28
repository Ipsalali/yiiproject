<?php

namespace WSUserActions\widgets\jsworker;

use yii\web\AssetBundle;

class JsWorkerAsset extends AssetBundle{

	public $sourcePath = '@backend/web/jsworker';
	public $css = [
		//'css/jsworker.css'
	];

	public $js = [
		'chatState.js',
		'client.js',
	];

	public $depends = ['yii\web\YiiAsset','yii\bootstrap\BootstrapAsset'];
}

?>