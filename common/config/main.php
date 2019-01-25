<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'common\rbac\DbManager',
  		]
    ],
    'modules' => [
        'websocket' => [
            'class' => 'WSUserActions\WSWorker'
        ],
        // 'profiler'=>[
        //     'class' => 'backend\modules\profiler\Profiler'
        // ],
        
    ],
];
