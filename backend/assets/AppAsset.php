<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/jquery.minicolors.css',
        'css/metisMenu.min.css',
        'css/sb-admin-2.css',
        'css/morris.css',
        'css/font-awesome.min.css',
    ];
    public $js = [
        'js/jquery.minicolors.js',
        'js/bootstrap.min.js',
        'js/metisMenu.min.js',
        'js/raphael.min.js',
        'js/sb-admin-2.js',
        'js/main.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
