<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'common\rbac\DbManager',
  		],
  		'webservice1C'=>[
  			'class'=>'soapclient\SClient',
  			'wsdl'=>'http://62.148.16.218/bosfor2017/ws/ws1.1cws?wsdl',
  			'username'=>'гамзат',
  			'password'=>'гамзат'
  		]
    ],
];
