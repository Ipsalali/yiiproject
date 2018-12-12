<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\modules\RequestSearch;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use soapclient\methods\Useraccountload;
use common\models\Request;
use common\models\Raport;

/**
 * Requests controller
 */
class RequestsController extends Controller
{   

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
                        'actions' => ['index','list','exec-all','exec-unloadremnant','exec-useraccountload','exec-raportload','exec-calcsquare','error'],
                        'allow' => true,
                        'roles'=>['superadmin']
                    ],
                    
                ],
            ]
        ];
    }
    

    public function actionIndex(){
       

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

            $RequestSearch = new RequestSearch;
        
            $dataProvider = $RequestSearch->search(Yii::$app->request->get());

            $view = $this->renderPartial('monitoring',['dataProvider'=>$dataProvider,'RequestSearch'=>$RequestSearch]);

            return ['view'=>$view,'date'=>date("d.m.Y H:i:s",time()),'post'=>Yii::$app->request->queryParams];
        
        }else{

            $RequestSearch = new RequestSearch;
        
            $dataProvider = $RequestSearch->search(Yii::$app->request->get());

            $view = $this->renderPartial('monitoring',['dataProvider'=>$dataProvider,'RequestSearch'=>$RequestSearch]);

            return $this->render('index',['view'=>$view]);
        }
       
    }


    public function actionList(){


        return $this->render('list',[]);
    }


    public function actionExecAll(){
        return $this->render('result',[]);
    }



    public function actionExecUnloadRemnant(){
        return $this->render('result',[]);
    }


    public function actionExecUseraccountload(){


        try {
            
            $data =[
                'guid'=>'e47d2a80-2be2-4767-953e-21f17b623987',
                'password'=>'123456'
            ];
            $method = new Useraccountload($data);

            $request = new Request([
                'request'=>get_class($method),
                'params_in'=>json_encode($method->attributes),
                'user_id'=>null,
                'actor_id'=>null
            ]);

            if($request->save() && $request->send($method)){
                      
            }else{
                    
            }

        }catch(\Exception $e) {
            throw $e;
        }

        print_r($request->params_out);
        exit;
        return $this->render('result',[]);
    }


    public function actionExecRaportload(){


        $model = Raport::findOne(['id'=>1]);

        if(!isset($model->id))
            throw new \Exception("Документ не найден!",404);

        try {
            $model->sendToConfirmation();
        } catch (\Exception $e) {
            
        }
        

        return $this->redirect(['requests/index']);
    }


    public function actionExecCalcsquare(){
        return $this->render('result',[]);
    }

    
   
}
