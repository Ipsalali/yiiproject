<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Seller;
use frontend\modules\SellersSearch;

class SellersController extends Controller{


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
                        'roles' => ['sellers'],
                    ]
                ],
            ]
        ];
    }
    



    public function actionIndex(){

        $modelFilters = new SellersSearch;
        $dataProvider = $modelFilters->search(Yii::$app->request->queryParams);
        return $this->render('index',array('dataProvider'=>$dataProvider,'modelFilters'=>$modelFilters));
    }





    public function actionCreate(){
        $model = new Seller;

        if(isset($_POST['Seller'])){
            if($model->load($_POST) && $model->validate()){
                
                $model->setPassword($model->password);
                $model->generateAuthKey();
                
                if($model->save()){
                    $user_id = $model->id;
                    $seller = Yii::$app->authManager->getRole('seller');
                    Yii::$app->authManager->assign($seller, $user_id);
                }else{
                    Yii::$app->session->setFlash("warning","Поставщик не добавлен");
                    return $this->render('create',array('model'=>$model));
                }
                
                Yii::$app->session->setFlash("success","Поставщик добавлен");
                return Yii::$app->response->redirect(array("sellers/index"));
            } 
        }

        return $this->render('create',array('model'=>$model));
    }





    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $model = Seller::getSellers($id);
        
        if(!isset($model->id))
            throw new HttpException(404,'Document Does Not Exist');
    
        
        return $this->render('read',array("model"=>$model));
    }




    public function actionUpdate($id = null){
        if($id == null)
            throw new HttpException(404, 'Not Found');

        $model = Seller::getSellers($id);

        if(!isset($model->id))
            throw new HttpException(404, 'Document Does Not Exist');
        
        
        
        if (isset($_POST['Seller']))
        {   
            if ($model->load($_POST) && $model->validate()){
                
                
                
                
                if(isset($_POST['change_password'])){
                    $model->setPassword($model->password);
                    $model->generateAuthKey();
                    Yii::$app->session->setFlash("success","Пароль изменен!");
                }
                
                
                
                if($model->save()){
                    Yii::$app->session->setFlash("success","Данные поставщика изменены!");
                    return Yii::$app->response->redirect(array('sellers/index'));
                }else{
                    Yii::$app->session->setFlash("warning","Данные поставщика не изменены!");
                }
            }
            
            
        }

        return $this->render('create', array(
            'model' => $model
        ));

    }




    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("danger","Поставщик не найден");
            return Yii::$app->response->redirect(array("sellers/index"));
        }

        $model = Seller::getSellers($id);

        if(!isset($model->id)){
            Yii::$app->session->setFlash("danger","Поставщик не найден");
            return Yii::$app->response->redirect(array("sellers/index"));
        }

        $model->delete();
        
        Yii::$app->session->setFlash("success","Поставщик удален из базы");
        return Yii::$app->response->redirect(array("sellers/index"));
    }

    

}