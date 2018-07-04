<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\TypePackaging;

class TypepackagingController extends Controller{


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

        $type = new TypePackaging;

        $data = $type->find()->all();


        return $this->render('index',array('data'=>$data,'model'=>$type));
    }





    public function actionCreate(){
        $model = new TypePackaging;

        if(isset($_POST['TypePackaging'])){
            if($model->load($_POST) && $model->save()){
                Yii::$app->response->redirect(array("typepackaging/index"));
            } 
        }

        return $this->render('create',array('model'=>$model));
    }





    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $model = TypePackaging::findOne($id);

        if($model === NULL)
            throw new HttpException(404,'Document Does Not Exist');

        return $this->render('read',array("model"=>$model));

    }




    public function actionUpdate($id = null){
        if($id == null)
            throw new HttpException(404, 'Not Found');

        $model = TypePackaging::findOne($id);

        if ($model === NULL)
            throw new HttpException(404, 'Document Does Not Exist');
        
        if (isset($_POST['TypePackaging']))
        {
            if ($model->load($_POST) && $model->save())
                Yii::$app->response->redirect(array('typepackaging/index'));
        }

        return $this->render('create', array(
            'model' => $model
        ));

    }




    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("typepackaging/index"));
        }

        $model = TypePackaging::findOne($id);

        if($model === NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("typepackaging/index"));
        }


        $model->delete();

        Yii::$app->session->setFlash("StatusDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        Yii::$app->response->redirect(array("typepackaging/index"));
    }

    

}