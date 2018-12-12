<?php

namespace api\rest\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;

use yii\rest\ActiveController;
use yii\rest\Controller;

class ProfileController extends Controller
{
    public function behaviors(){
	    $behaviors = parent::behaviors();
	    $behaviors['authenticator'] = [
	        'class' => CompositeAuth::className(),
	        'authMethods' => [
	        	[
	        		'class'=>HttpBasicAuth::className(),
	        		'auth'=>function ($login, $password) {
						$user = \common\models\User::findByLogin($login);
						return $user && $user->validatePassword($password) ? $user : null;
					}
	        	],
	        	// [
	        	// 	'class'=>HttpBearerAuth::className(),
	        	// 	'only'=>'index'
	        	// ]
	        ],
	    ];

	    $behaviors[ 'access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [	
                	'actions'=>['index'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
                // [
                // 	'actions'=>['auth'],
                //     'allow' => true,
                //     'roles' => ['?'],
                // ],
            ],
        ];
	    return $behaviors;
	}


	public function actionIndex(){

		$user = Yii::$app->user->identity;
		return $user;
	}


	// public function actionAuth(){

	// 	return 'auth';
	// }
}