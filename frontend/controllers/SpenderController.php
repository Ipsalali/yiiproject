<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Spender;
use common\models\Client;
use yii\web\HttpException;

class SpenderController extends Controller{

	

	/**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'get-client','send'],
                        'allow' => true,
                        'roles' => ['client/index'],
                    ],
                ],
            ]
        ];
    }

    public function actionIndex(){

        $spender = new Spender;

        return $this->render('spender',['spender'=>$spender]);
    }


    public function actionSend(){

    	if(Yii::$app->request->isAjax){
    		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
	    	$params = Yii::$app->request->post();
            
	    	if(isset($params['Spender']) && count($params['Spender'])){
	    		$Spender = new Spender;
                
	    		if($Spender->load($params) && $Spender->save() && $Spender->sendLetter()){
	    			return ['result'=>1];
	    		}
	    	}else{
	    		return ['result'=>0];
	    	}
    	}else{
    		return Yii::$app->response->redirect(['spender/index']);
    	}
    	
    }


    public function actionGetClient(){
        if(Yii::$app->request->isAjax){

            $params = Yii::$app->request->get();

            $answer = array();

            if($params['key']){
            
                $key = trim(strip_tags($params['key']));

                $clients = Client::searchByKey($key);
                if(is_array($clients) && count($clients)){
                    $answer['result'] = 1;
                    $answer['key'] = $key;
                    $answer['client'] = $clients;
                }else{
                	$answer['result'] = 0;
                    $answer['error']['text'] = 'not found clients';
                }
            }else{
                $answer['result'] = 0;
            }
        
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            return $answer;
        }else{
            return Yii::$app->response->redirect(['spender/index']);
        }
    }
	

}