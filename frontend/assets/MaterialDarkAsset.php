<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MaterialDarkAsset extends AssetBundle
{
    public $basePath = '@webroot/templates/material-dark';
    public $baseUrl = '@web/templates/material-dark';
    public $css = [
        // custom
        'custom/css/jquery-ui.min.css',
        
        //'bootstrap/bootstrap.css',
        'fonts/fonts.css',
        'fonts/fonts-awesome.css',
        'css/material-dashboard.css',

        //custom
        'custom/css/site.css',
    ];
    public $js = [
        'js/core/jquery.min.js',
        'js/core/popper.min.js',
        'js/core/bootstrap-material-design.min.js',
        'js/plugins/perfect-scrollbar.jquery.min.js',
        'js/plugins/chartist.min.js',
        'js/plugins/bootstrap-notify.js',
        'js/material-dashboard.min.js',

        // custom
        'custom/js/jquery-ui.min.js',
        'custom/js/main.js'
    ];
    public $depends = [
        'yii\web\YiiAsset'
    ];
    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];
}
