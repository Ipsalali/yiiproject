<?php


namespace frontend\bootstrap4;

use yii\web\AssetBundle;


class Bootstrap4PluginAsset extends AssetBundle
{	
	
	public $basePath = '@webroot';
    public $baseUrl = '@web/bootstrap';

    public $js = [
        'js/bootstrap.js',
    ];

}
