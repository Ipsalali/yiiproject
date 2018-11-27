<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Spender;

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
                        'actions' => ['index','emails','check-email'],
                        'allow' => true,
                        'roles' => ["admin"],
                    ]
                ],
            ]
        ];
    }
    



    public function actionIndex(){

        return $this->render('index',[]);
    }



    public function actionEmails(){

        $spender = new Spender();

        $emails = $spender->getEmailsForSend();

        return $this->render('emails',[
            'spender'=>$spender,
            'emails'=>$emails
        ]);
    }



    public function actionCheckEmail($email = null){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $api_key = 'secret_6497a32ac5be390a97ac0a42c5f48e0b';
            \NeverBounce\Auth::setApiKey($api_key);
            
            $response  = \NeverBounce\Single::check($email);
            
            return [
                'success'=>$response ->is("valid"),
                'email'=>$email
            ];
        }else{
            return $this->redirect(['spender/emails']);
        }
    }

}