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
	    	$post = Yii::$app->request->post();
            

	    	if(isset($post['Spender']) && count($post['Spender'])){
	    		$Spender = new Spender;

	    		if($Spender->load($post) && $Spender->save() && $Spender->sendLetter()){
	    			//Yii::$app->session->setFlash("SpenderSaved");
	    			return ['result'=>1];
	    		}
	    	}else{
	    		return ['result'=>0];
	    		//Yii::$app->session->setFlash("SpenderError");
	    	}
    	}else{
    		return Yii::$app->response->redirect(['spender/index']);
    	}
    	
    }


    public function actionGetClient(){
        if(Yii::$app->request->isAjax){

            $post = Yii::$app->request->post();

            $answer = array();

            if($post['key']){
            
                $key = trim(strip_tags($post['key']));

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
        }
    }
	

}