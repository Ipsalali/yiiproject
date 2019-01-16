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
				'title'=>'Россия',
				'contacts'=>[
					'Центральный офис'=>'Москва, Краснопресненская набережная дом 12. ЦМТ',
					'Телефон'=>" +7 (495) 781-57-45",
					'Филиал'=>'Москва, ул. Сельскохозяйственная, дом 12Б, 2 этаж, офис 3.',
					'E-mail'=>'info@tedrans.com'
				]
			],

			[
				'title'=>'Турция',
				'contacts'=>[
					'Адрес'=>'Стамбул,  İnönü Mahallesi Muammer Aksoy Caddesi No:102  Tilkici Tır Tesisleri Sefaköy - Küçükçekmece / İstanbul',
					'Телефон'=>"+90212 812 57 62",
					'Телефон'=>'+90555 034 5762 - Yunus Isitman',
					'E-mail'=>'istanbul@tedtrans.com'
				]
			],

			[
				'title'=>'ОАЭ',
				'contacts'=>[
					'Адрес'=>'Дубай, ICT General Treding Al Masaeed Building Al Maktoum Street. 5th Floor 504 Dubai — U.A.E',
					'Телефон'=>"+9 (71) 422 474 32",
					'Факс'=>'+9 (71) 422 474 31',
					'Телефон'=>'+9 (71) 503 503 702',
					'E-mail'=>'bhammoud@ictdxb.ae'
				]
			],

			[
				'title'=>'Литва',
				'contacts'=>[
					'Адрес'=>'UAB "JML Group" Киртиму ул. 53, LT-02244, Вильнюс, Литва',
					'Телефон'=>"Loreta +370 620 626 74",
					'Код таможни'=>'VA00376',
				]
			],

			[
				'title'=>'Китай',
				'contacts'=>[
					'Адрес'=>'1107#, 3 Building , ShengHe Plaza , NanHuan Road , HouJie , DongGuan , GuangDong , China',
					'Телефон'=>"0086-769-85985272",
					'Телефон'=>'0086-13546970312',
					'Факс'=>'0086-769-85898691',
					'E-mail'=>'Blany_liu@163.com'
				]
			],

		];
	}

}