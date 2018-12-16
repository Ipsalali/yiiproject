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

class ProfileController extends Controller
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
                	'actions'=>['index','orders'],
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
            'orders' => ['get'],
        ];
    }

	public function actionIndex(){
		
		$data = array();
		$user = Yii::$app->user->identity;
		$data['email'] = $user->email;
		$data['phone'] = $user->phone;

		$client = $user->client;
		if(isset($client->id)){
			$manager = $client->managerUser;
			if(isset($manager->id)){
				$data['manager']['name'] = $manager->name;
				$data['manager']['phone'] = $manager->phone;
				$data['manager']['email'] = $manager->email;
			}
		}

		$data['debt'] = $user->getManagerSverka();

		return $data;
	}



	public function actionOrders(){
		
		$data = array();
		$user = Yii::$app->user->identity;
		$client = $user->client;

		if(isset($client->id)){
			$oFilter = new OrderFilter();
			$oFilter->setClient($client->id);
			$orders = $oFilter->filter(['OrderFilter'=>Yii::$app->request->queryParams]);
			$orders = $client->getOwnAutotrucksWithApps($orders);
			$formatted = array();
			$data['orders'] = array();
			foreach ($orders as $item) {
				$formatted['name'] = $item['name'];
				$formatted['course'] = $item['course'];
				$formatted['country'] = $item['countryName'];
				$formatted['auto_number'] = $item['auto_number'];
				$formatted['gtd'] = $item['gtd'];
				$formatted['status'] = $item->status ? $item->activeStatus->title : "";
				$items  = $item['appsCollection'];
				$formatted['items'] = array();
				$i = array();
				foreach ($items as $app) {
					$i['sender'] = $app['senderName'];
					$i['info'] = $app['info'];
					
					if(!$app['type']){
						$i['package'] = $app['package'] ? $app['packageTitle'] : "Не указан";
						$i['count_place'] = $app['count_place'];
					}
					

					$i['rate'] = $app['rate'];
					$i['weight'] = $app['type']? '' : $app['weight'];
					$i['summa_us'] = $app['summa_us'];
					
					$rate_vl = $app['weight'] > 0 ? $app['summa_us']/$app['weight'] : 0;
					$sum_ru = $app['weight'] * $rate_vl * $item['course'];
					$i['summa_ru'] = $app['type'] ? round($app['rate']*$item['course'],2) : round($sum_ru,2);
					$i['comment'] = $app['comment'];

					array_push($formatted['items'], $i);	
				}
				array_push($data['orders'], $formatted);
			}
		}

		

		return $data;
	}

}