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
            'class' => 'WSUserActions\WSWorker',
            'websocket_host'=>'websocket://127.0.0.1',
            'websocket_port'=>18000
        ],
        // 'profiler'=>[
        //     'class' => 'backend\modules\profiler\Profiler'
        // ],
        
    ],
];
