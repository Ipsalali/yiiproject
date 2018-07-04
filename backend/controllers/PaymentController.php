<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Payment;

class PaymentController extends Controller{

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
                        'actions' => ['index', 'create','update','read','delete'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ],
            ]
        ];
    }
    


    public function actionIndex(){
        $data = Payment::find()->all();
        return $this->render('index',array('data'=>$data));
    }




    public function actionCreate($id = null){
        
        if((int)$id){
            $model = Payment::findOne($id);
        }

        if(!isset($model) || !isset($model->id) || !$model->id)
            $model = new Payment;

        if(isset($_POST['Payment'])){
            
            if($model->load($_POST) && $model->save()){
                return Yii::$app->response->redirect(array("payment/index"));
            }

        }

        $data = Payment::find()->all();
        return $this->render('create',array('model'=>$model,'data'=>$data));
    }



    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("PaymentDeleteError");
            Yii::$app->response->redirect(array("payment/index"));
        }

        $model = Payment::findOne($id);

        if($model === NULL){
            Yii::$app->session->setFlash("PaymentDeleteError");
            Yii::$app->response->redirect(array("payment/index"));
        }


        $model->delete();

        Yii::$app->session->setFlash("PaymentDeleted");
        Yii::$app->response->redirect(array("payment/index"));
    }

    

}