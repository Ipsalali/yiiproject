<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\SupplierCountry;

class SuppliercountryController extends Controller{


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
                        'actions' => ['index', 'create','read','delete','update'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ],
            ]
        ];
    }
    



    public function actionIndex(){

        $country = new SupplierCountry;

        $data = $country->find()->all();


        return $this->render('index',array('data'=>$data,'model'=>$country));
    }





    public function actionCreate(){
        $model = new SupplierCountry;

        if(isset($_POST['SupplierCountry'])){
            $model->country = $_POST['SupplierCountry']['country'];
            $model->code = $_POST['SupplierCountry']['code'];
            if($model->save()){
                Yii::$app->response->redirect(array("suppliercountry/index"));
            } 
        }

        return $this->render('create',array('model'=>$model));
    }





    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $model = SupplierCountry::findOne($id);

        if($model === NULL)
            throw new HttpException(404,'Document Does Not Exist');

        return $this->render('read',array("model"=>$model));

    }




    public function actionUpdate($id = null){
        if($id == null)
            throw new HttpException(404, 'Not Found');

        $model = SupplierCountry::findOne($id);

        if ($model === NULL)
            throw new HttpException(404, 'Document Does Not Exist');
        
        if (isset($_POST['SupplierCountry']))
        {
            $model->country = $_POST['SupplierCountry']['country'];
            $model->code = $_POST['SupplierCountry']['code'];
            if ($model->save())
                Yii::$app->response->redirect(array('suppliercountry/index'));
        }

        return $this->render('create', array(
            'model' => $model
        ));

    }




    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("suppliercountry/index"));
        }

        $model = SupplierCountry::findOne($id);

        if($model === NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("suppliercountry/index"));
        }


        $model->delete();

        Yii::$app->session->setFlash("StatusDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        Yii::$app->response->redirect(array("suppliercountry/index"));
    }

    

}