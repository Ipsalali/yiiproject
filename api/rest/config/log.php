<?php

return [
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile'=>'@api/rest/logs/info_Log.txt',
                'logVars'=>[],
                'categories'=>['api'],
            ],
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile'=>'@api/rest/logs/sessions_Log.txt',
                'logVars'=>[],
                'categories'=>['api_sessions'],
            ],
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile'=>'@api/rest/logs/states_Log.txt',
                'logVars'=>[],
                'categories'=>['api_states'],
            ],
            [
                'class' => 'yii\log\FileTarget',
                'levels' => ['info'],
                'logFile'=>'@api/rest/logs/test_Log.txt',
                'logVars'=>[],
                'categories'=>['api_tests'],
            ]
        ];
    

?>