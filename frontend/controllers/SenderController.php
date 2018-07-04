<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Sender;
use frontend\modules\SendersSearch;

class SenderController extends Controller{


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
                        'roles' => ['sender'],
                    ]
                ],
            ]
        ];
    }
    



    public function actionIndex(){

        $model = new Sender;

        $modelFilters = new SendersSearch;
        $dataProvider = $modelFilters->search(Yii::$app->request->queryParams);
        return $this->render('index',array('dataProvider'=>$dataProvider,'modelFilters'=>$modelFilters,'model'=>$model));
    }





    public function actionCreate(){
        $model = new Sender;

        if(isset($_POST['Sender'])){
            if($model->load($_POST) && $model->save()){
               return Yii::$app->response->redirect(array("sender/index"));
            } 
        }

        return $this->render('create',array('model'=>$model));
    }





    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $model = Sender::findOne($id);

        if($model === NULL)
            throw new HttpException(404,'Document Does Not Exist');

        return $this->render('read',array("model"=>$model));

    }




    public function actionUpdate($id = null){
        if($id == null)
            throw new HttpException(404, 'Not Found');

        $model = Sender::findOne($id);

        if ($model === NULL)
            throw new HttpException(404, 'Document Does Not Exist');
        
        if (isset($_POST['Sender']))
        {
            if ($model->load($_POST) && $model->save())
               return Yii::$app->response->redirect(array('sender/index'));
        }

        return $this->render('create', array(
            'model' => $model
        ));

    }




    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            return Yii::$app->response->redirect(array("sender/index"));
        }

        $model = Sender::findOne($id);

        if($model === NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            return Yii::$app->response->redirect(array("sender/index"));
        }


        $model->delete();

        Yii::$app->session->setFlash("StatusDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        return Yii::$app->response->redirect(array("sender/index"));
    }

    

}