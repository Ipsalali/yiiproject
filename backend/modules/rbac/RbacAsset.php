<?php

namespace backend\modules\rbac;

use yii\web\AssetBundle;

/**
 * Widget asset bundle
 */
class RbacAsset extends AssetBundle
{
	/**
	 * @inheritdoc
	 */
	public $sourcePath = '@backend/modules/rbac/assets';


	public $css = [
        'css/rbac.css'
    ];


    public $js = [
        'js/rbac.js'
    ];

    /**
	 * @inheritdoc
	 */
	public $depends = [
		'yii\web\JqueryAsset'
	];

}
