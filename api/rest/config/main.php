<?php
$params = array_merge(
    require(__DIR__ . '/../../../common/config/params.php'),
    require(__DIR__ . '/../../../common/config/params-local.php'),
    require(__DIR__ . '/params.php')
);

$log_targets = require __DIR__ . '/log.php';

return [
    'id' => 'app-rest',
    'basePath' => dirname(__DIR__),
    'language' => 'ru-RU',
    'controllerNamespace' => 'api\rest\controllers',
    'bootstrap' => ['log'],
    'components' => [
        'user' => [
            'identityClass' => 'api\rest\models\User',
            'enableSession'=>false,
            'enableAutoLogin' => false
        ],
		'request' => [
            'cookieValidationKey' => 'H5WJcheZiDbtBSN2s-XiiaaGTvzQMlfo',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'application/xml' => 'yii\web\XmlParser',
            ],
        ],
        'response' => [
            'class'=>"yii\web\Response",
            'format' => yii\web\Response::FORMAT_JSON,
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    // $response->data = [
                    //     'success' => $response->isSuccessful,
                    //     'data' => $response->data,
                    // ];
                    $response->statusCode = 200;
                }
            },
            'formatters' => [
                'json' => [
                    'class' => 'api\rest\formatters\PrettyJsonResponseFormatter',
                    'prettyPrint' => true,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
            'charset' => 'UTF-8',
        ],
		'urlManager' => [
		    'enablePrettyUrl' => false,
		    'enableStrictParsing' => false,
		    'showScriptName' => false,
		    'rules' => [
		        'auth'=>'auth/index',
                'profile'=>'profile/index',
                'orders'=>'profile/orders',
                'contacts'=>'resources/contacts',
		    ],
		],
        'log' => [
            'traceLevel'=> YII_DEBUG ? 3 : 0,
            'targets' => $log_targets,
        ],
    ],
    'params' => $params,
];
