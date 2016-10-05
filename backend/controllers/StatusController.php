<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Status;

class StatusController extends Controller{

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
                        'actions' => ['update', 'index'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['read', 'index'],
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

        $status = new Status;

        $data = $status->find()->all();


        return $this->render('index',array('data'=>$data));
    }

    public function actionCreate(){
        $model = new Status;

        if(isset($_POST['Status'])){
            $model->title = $_POST['Status']['title'];
            $model->sort = ($_POST['Status']['sort'])?(int)$_POST['Status']['sort']:0;
            $model->description = $_POST['Status']['description'];
            $model->notification_template = $_POST['Status']['notification_template'];
            if($model->save()){
                Yii::$app->response->redirect(array("status/read",'id'=>$model->id));
            } 
        }

        return $this->render('create',array('model'=>$model));
    }

    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $status = Status::findOne($id);

        if($status === NULL)
            throw new HttpException(404,'Document Does Not Exist');

        return $this->render('read',array("status"=>$status));

    }

    public function actionUpdate($id = null){
        if($id == null)
            throw new HttpException(404, 'Not Found');

        $status = Status::findOne($id);

        if ($status === NULL)
            throw new HttpException(404, 'Document Does Not Exist');
        
        if (isset($_POST['Status']))
        {
            $status->title = $_POST['Status']['title'];
            $status->sort = ($_POST['Status']['sort'])?(int)$_POST['Status']['sort']:0;
            $status->description = $_POST['Status']['description'];
            $status->notification_template = $_POST['Status']['notification_template'];
            
            if ($status->save())
                Yii::$app->response->redirect(array('status/read', 'id' => $status->id));
        }

        return $this->render('create', array(
            'model' => $status
        ));

    }

    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("status/index"));
        }

        $status = Status::findOne($id);

        if($status === NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("status/index"));
        }


        $status->delete();

        Yii::$app->session->setFlash("StatusDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        Yii::$app->response->redirect(array("status/index"));
    }

}