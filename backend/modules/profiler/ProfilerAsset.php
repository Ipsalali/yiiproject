<?php

namespace backend\modules\profiler;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class ProfilerAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@backend/modules/profiler/assets';


	public $css = [
        'css/profiler.css'
    ];


    public $js = [
        'js/profiler.js'
    ];

    /**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];

}
