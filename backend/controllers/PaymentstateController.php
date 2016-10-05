<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\PaymentState;

class PaymentstateController extends Controller{


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
                        'actions' => ['index', 'index'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['create', 'index'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['delete', 'index'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ],
            ]
        ];
    }
    
    public function actionIndex(){

        $model = new PaymentState;

        $data = $model->find()->all();

        return $this->render('index',array('list'=>$data,'model'=>$model));
    }

    public function actionCreate(){
        $model = new PaymentState;

        $data = $model->find()->all();
        if(isset($_GET) && (int)$_GET['id']){
            $model = PaymentState::findOne((int)$_GET['id']);
        }

        if(isset($_POST['PaymentState'])){

            $model->title = trim(strip_tags($_POST['PaymentState']['title']));
            $model->color = $_POST['PaymentState']['color'];
            $model->default = (int)$_POST['PaymentState']['default'];
            if($model->save()){
                if((int)$_POST['PaymentState']['default']){
                    PaymentState::setDefault($model);
                }
                Yii::$app->response->redirect(array("paymentstate/index"));
            } 
        }

        return $this->render('index',array('model'=>$model,'list'=>$data));
    }


    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("PaymentStateDeleteError");
            Yii::$app->response->redirect(array("paymentstate/index"));
        }

        $model = PaymentState::findOne($id);

        if($model === NULL){
            Yii::$app->session->setFlash("PaymentStateDeleteError");
            Yii::$app->response->redirect(array("paymentstate/index"));
        }


        $model->delete();

        Yii::$app->session->setFlash("PaymentStateDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        Yii::$app->response->redirect(array("paymentstate/index"));
    }

}