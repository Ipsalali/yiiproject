<?php

return [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info','warning','error'],
                'logFile'=>'@api/rest/logs/info_Log.log',
                'logVars'=>['info','warning','error'],
                'categories'=>['api'],
            ],
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile'=>'@api/rest/logs/sessions_Log.log',
                'logVars'=>[],
                'categories'=>['api_sessions'],
            ],
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile'=>'@api/rest/logs/states_Log.log',
                'logVars'=>[],
                'categories'=>['api_states'],
            ],
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile'=>'@api/rest/logs/test_Log.log',
                'logVars'=>[],
                'categories'=>['api_tests'],
            ]
        ];
    

?>