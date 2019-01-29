<?php
namespace WSUserActions\controllers;

use Yii;

use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use WSUserActions\models\UserAction;

/**
 * Worker controller
 */
class JsWorkerController extends Controller
{   


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
                        'actions' => ['register-event','remove-event'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ]
        ];
    }

    

    
    public function actionRegisterEvent(){

        if(Yii::$app->request->getIsAjax()){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $post = Yii::$app->request->post();

            $post['user_id'] = Yii::$app->user->id;
            
            $action = UserAction::register($post);

            return isset($action->id) && $action->id;

        }else{
            return Yii::$app->response->redirect(['site/index']);
        }
    }


    public function actionRemoveEvent(){

        if(Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $action = UserAction::deleteAll();
            
            return $action;

        }else{
            return Yii::$app->response->redirect(['site/index']);
        }
        
    }
    
}
