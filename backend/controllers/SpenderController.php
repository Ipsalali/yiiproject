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
                        'actions' => ['index','emails'],
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


}