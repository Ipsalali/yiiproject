<?php

namespace common\widgets\autocomplete;

use yii\web\AssetBundle;

class AutoCompleteAsset extends AssetBundle{

	public $sourcePath = '@common/widgets/autocomplete/assets';
	
	public $css = [
        'css/jquery.ui.min.css',
    ];

    

	public $js = [
		"js/jquery.ui.js"
	];

	public $depends = [
        'yii\web\JqueryAsset',
    ];

}

?>