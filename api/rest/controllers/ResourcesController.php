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
use api\rest\modules\OrderFilter;

class ResourcesController extends Controller
{
    public function behaviors(){
	    $behaviors = parent::behaviors();
	    $behaviors['authenticator'] = [
	        'class' => CompositeAuth::className(),
	        'authMethods' => [
	        	HttpBearerAuth::className(),
	        	[
	        		'class'=>HttpBasicAuth::className(),
	        		'auth'=>function ($login, $password) {
						$user = \common\models\User::findByLogin($login);
						return $user && $user->validatePassword($password) ? $user : null;
					}
	        	]
	        ],
	    ];

	    $behaviors[ 'access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [	
                	'actions'=>['index','contacts'],
                    'allow' => true,
                    'roles' => ['@'],
                ],
            ],
        ];
	    return $behaviors;
	}


	protected function verbs()
    {
        return [
            'index' => ['get'],
            'contacts' => ['get'],
        ];
    }

	public function actionIndex(){
		
		return [];
	}



	public function actionContacts(){
		
		
		return [
			[
				'name'=>'Test'
			]

		];
	}

}