<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\PaymentState;

/**
*  Старый функционал, рассмотреть удаление
*
*
*/
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
                        'actions' => ['index', 'create','delete'],
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
            $model->default_value = (int)$_POST['PaymentState']['default_value'];
            $model->end_state = (int)$_POST['PaymentState']['end_state'];
            $model->sum_state = (int)$_POST['PaymentState']['sum_state'];
            //$model->filter = ((int)$_POST['PaymentState']['filter'])? 1 : 0 ;
            if($model->save()){
                if((int)$_POST['PaymentState']['default_value']){
                    PaymentState::setDefault($model);
                }

                if((int)$_POST['PaymentState']['end_state']){
                    PaymentState::setEndState($model);
                }

                // if((int)$_POST['PaymentState']['sum_state']){
                //     PaymentState::setSumState($model);
                // }
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