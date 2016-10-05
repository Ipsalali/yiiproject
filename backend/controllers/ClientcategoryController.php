<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\ClientCategory;

class ClientcategoryController extends Controller{


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

        $client = new ClientCategory;

        $data = $client->find()->all();


        return $this->render('index',array('data'=>$data));
    }

    public function actionCreate(){
        $model = new ClientCategory;

        if(isset($_POST['ClientCategory'])){
            $model->cc_title = $_POST['ClientCategory']['cc_title'];
            $model->cc_description = $_POST['ClientCategory']['cc_description'];
            $model->cc_parent = ($_POST['ClientCategory']['cc_parent']) ? (int)$_POST['ClientCategory']['cc_parent'] :0;
            if($model->save()){
                Yii::$app->response->redirect(array("clientcategory/read",'id'=>$model->cc_id));
            } 
        }

        return $this->render('create',array('model'=>$model));
    }

    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $category = ClientCategory::findOne($id);

        if($category === NULL)
            throw new HttpException(404,'Document Does Not Exist');

        return $this->render('read',array("category"=>$category));

    }

    public function actionUpdate($id = null){
        if($id == null)
            throw new HttpException(404, 'Not Found');

        $model = ClientCategory::findOne($id);

        if ($model === NULL)
            throw new HttpException(404, 'Document Does Not Exist');
        
        if (isset($_POST['ClientCategory']))
        {
            $model->cc_title = $_POST['ClientCategory']['cc_title'];
            $model->cc_description = $_POST['ClientCategory']['cc_description'];
            $model->cc_parent = ($_POST['ClientCategory']['cc_parent']) ? (int)$_POST['ClientCategory']['cc_parent'] :0;
            
            if ($model->save())
                Yii::$app->response->redirect(array('clientcategory/index', 'id' => $model->cc_id));
        }

        return $this->render('create', array(
            'model' => $model
        ));

    }

    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("clientcategory/index"));
        }

        $Category = ClientCategory::findOne($id);

        if($Category === NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("clientcategory/index"));
        }


        $Category->delete();

        Yii::$app->session->setFlash("StatusDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        Yii::$app->response->redirect(array("clientcategory/index"));
    }

}