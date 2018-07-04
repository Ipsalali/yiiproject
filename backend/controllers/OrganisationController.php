<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use common\models\Organisation;

class OrganisationController extends Controller{


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
                        'actions' => ['index','create','update','read','remove','toactive','stop'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ],
            ]
        ];
    }
    



    public function actionIndex(){

        $org = new Organisation;

        $orgs = $org->find()->all();

        return $this->render('index',array('orgs'=>$orgs));
    }




    public function actionCreate(){
        $model = new Organisation;

        if(isset($_POST['org_id']) && (int)$_POST['org_id']){
            $model = Organisation::findOne((int)$_POST['org_id']); 
        }

        if(isset($_POST['Organisation'])){
            
            if(isset($_POST['Organisation']['payment']) && (int)$_POST['Organisation']['payment']){
                $model->scenario = Organisation::SCENARIO_CASH;
            }
            else{
                $model->scenario = 'default';
            } 

            if($model->load($_POST) && $model->save()){
                Yii::$app->response->redirect(array("organisation/index"));
            }
        }

        $model->scenario = Organisation::SCENARIO_CASH;

        return $this->render('create',array('model'=>$model));
    }




    public function actionRead(){

        $org = new Organisation;
        $get = Yii::$app->request->get();
        $id = (int)$get['id'];
        
        if($id == NULL){
            Yii::$app->session->setFlash("NO_ID");
            Yii::$app->response->redirect(array("organisation/index"));
        }

        $org = Organisation::findOne($id);
        if(!$org->id){
            Yii::$app->session->setFlash("organisationNoExists");
            Yii::$app->response->redirect(array("organisation/index"));
        }

        return $this->render('read',array('org'=>$org));
    }




    public function actionRemove(){

        $post = Yii::$app->request->post();
        $id = (int)$post['org_id'];
        if($id == NULL){
            Yii::$app->session->setFlash("NO_ID");
            Yii::$app->response->redirect(array("organisation/index"));
        }

        $model = Organisation::findOne($id);

        if(!$model->id){
            Yii::$app->session->setFlash("organisationNoExists");
            Yii::$app->response->redirect(array("organisation/index"));
        }


        $model->delete();

        Yii::$app->session->setFlash("OrgDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        Yii::$app->response->redirect(array("organisation/index"));
    }




    public function actionToactive(){
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $response['result'] = 0;
            if((int)$post['active_org']){
                $org = Organisation::findOne((int)$post['active_org']);
                if($org->id){
                    if(!$org->active){
                        $org->active = 1;
                        if($org->save(false)){
                            Organisation::updateAll(['active' => 0],'id <> '.$org->id);
                            $response['text'] = $org->org_name." назначена активной.";
                            $response['result'] = 1;
                        }
                    }else{ $response['text'] = "Организация уже активная."; }
                }else{
                    $response['text'] = "Организация не была найдена.";
                }
            }else{
                $response['text'] = "Не переданы необходимые данные от клиента.";
            }

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $response;
        }
    }


    public function actionStop(){
        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $response['result'] = 0;
            if((int)$post['stop_org']){
                $org = Organisation::findOne((int)$post['stop_org']);
                if($org->id){
                    $toStope = isset($post['stop_org_status']) && (int)$post['stop_org_status'] ? true : false;
                    
                    $org->is_stoped = $toStope;
                    if($org->save(false)){
                        
                        $response['result'] = 1;
                    }

                    if($org->is_stoped){
                        $response['text'] = "Организация '{$org->org_name}' приостановлена."; 
                    }else{ 
                        $response['text'] = "Организация '{$org->org_name}' возобновлена."; 
                    }


                }else{
                    $response['text'] = "Организация не была найдена.";
                }
            }else{
                $response['text'] = "Не переданы необходимые данные от клиента.";
            }

            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return $response;
        }
    }

    

}