<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        
        
    ],
    'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\PathController',
            'access' => ['@'],
            'root' => [
                    'baseUrl'=>'@web',
                    'basePath'=>'@webroot',
                    'path' => 'files/',
                    'name' => 'Files'
                ],
            // 'watermark' => [
            //             'source'         => __DIR__.'/logo.png', // Path to Water mark image
            //              'marginRight'    => 5,          // Margin right pixel
            //              'marginBottom'   => 5,          // Margin bottom pixel
            //              'quality'        => 95,         // JPEG image save quality
            //              'transparency'   => 70,         // Water mark image transparency ( other than PNG )
            //              'targetType'     => IMG_GIF|IMG_JPG|IMG_PNG|IMG_WBMP, // Target image formats ( bit-field )
            //              'targetMinPixel' => 200         // Target image minimum pixel size
            //     ]
            ]
        ],
    'params' => $params,
];
