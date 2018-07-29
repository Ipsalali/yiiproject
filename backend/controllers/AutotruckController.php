<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Post;
use yii\web\HttpException;
use backend\models\Autotruck;
use backend\models\App;
use backend\models\AppTrace;
use common\models\Client;
use common\models\Status;
use common\models\User;
use common\helper\EDateTime;
use backend\modules\ListAction;
use backend\modules\AutotruckSearch;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class AutotruckController extends Controller{

	public $layout = "main.php";

	public function actions()
    {
        return [
            
        ];
    }

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
                        'actions' => ['index', 'error'],
                        'allow' => true,
                        'roles' => ['autotruck/index'],
                    ],
                    [
                        'actions' => ['create', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/create'],
                    ],
                    [
                        'actions' => ['read', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/read'],
                    ],
                    [
                        'actions' => ['update', 'index','status-remove'],
                        'allow' => true,
                        'roles' => ['autotruck/update'],
                    ],
                    [
                        'actions' => ['delete', 'delete-all'],
                        'allow' => true,
                        'roles' => ['autotruck/delete'],
                    ],
                    [
                        'actions' => ['removeappajax', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/update'],
                    ],
                    [
                        'actions' => ['addexpenses', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/addexpenses'],
                    ],
                    [
                        'actions' => ['removeexpajax', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/addexpenses'],
                    ],
                    [
                    	'actions'=>['report','index'],
                    	'allow' => true,
                    	'roles'=>['autotruck/report']
                    ]
                ],
            ]
        ];
    }



	public function actionIndex(){

		$autotruckSearch = new AutotruckSearch;
		$dataProvider = $autotruckSearch->search(Yii::$app->request->queryParams);
		$this->layout = "main.php";
		
		return $this->render('index',array('autotruckSearch'=>$autotruckSearch,'dataProvider'=>$dataProvider));
	}

	


	public function actionRead($id = NULL){

		$filters = (array_key_exists('filters', $_GET)) ? $_GET['filters'] : array();
		Yii::$app->view->params['filters'] = $filters;

		if($id == null)
			throw new HttpException(404,'Not Found!');

		$autotruck = Autotruck::findOne($id);

		
		if($autotruck === NULL)
			throw new HttpException(404,'Document Does Not Exist');

		
		$this->layout = "main.php";
		
		$query = $autotruck->find()->orderBy(['id'=>SORT_DESC]);
		Yii::$app->view->params['query'] = $query;
		return $this->render('read',array("autotruck"=>$autotruck));

	}

    public function actionUpdate($id = null){

        $filters = (array_key_exists('filters', $_GET)) ? $_GET['filters'] : array();
        Yii::$app->view->params['filters'] = $filters;

        if($id == null)
            throw new HttpException(404, 'Not Found');

        $autotruck = Autotruck::findOne($id);

        $listAutotruck = Autotruck::find()->orderBy('id')->all();
        
        $query = $autotruck->find()->orderBy(['id'=>SORT_DESC]);
        Yii::$app->view->params['query'] = $query;

        if ($autotruck === NULL)
            throw new HttpException(404, 'Document Does Not Exist');
        
        $post = Yii::$app->request->post();

        if($post['Autotruck']){

            $autotruck->name = $post['Autotruck']['name'];
            $autotruck->course = ($post['Autotruck']['course'])?round($post['Autotruck']['course'],4):0;
            $autotruck->country = $post['Autotruck']['country'];
            $autotruck->date = ($post['Autotruck']['date'])
                                    ? date('Y-m-d',strtotime($post['Autotruck']['date']))
                                    : $autotruck->date;
            
            $update = true;
            $status_update = ($autotruck->status == $post['Autotruck']['status'])? false : true;
            $prev_status = $autotruck->status;
            $autotruck->status = (!$status_update) ? $autotruck->status: (int)$post['Autotruck']['status'];
            $save_prevstatus = $status_update;
            

            $autotruck->description = $post['Autotruck']['description'];
            

            if($autotruck->save(1)){
                // статус заявки
                if($autotruck->id && $status_update){
                    $apptrace = new AppTrace;
                    $apptrace->autotruck_id = $autotruck->id;
                    $apptrace->status_id = $autotruck->status;
                    $apptrace->traсe_first = 0;
                    $apptrace->traсe_last = 1;
                    $apptrace->prevstatus_id =($save_prevstatus)? $prev_status : 0;
                    $apptrace->trace_date = ($post['Autotruck']['date_status'])
                                    ? date('Y-m-d',strtotime($post['Autotruck']['date_status'])):date("Y-m-d");

                    $apptrace->save();
                    Yii::$app->session->setFlash("AppTraceCreated");            
                }else{
                    Yii::$app->session->setFlash("AppTraceCreatedError");
                }


                //Добавление расхода
                if(isset($post['ExpensesManager']) && count($post['ExpensesManager']) && $autotruck->id){
                    foreach ($post['ExpensesManager'] as $key => $item) {
                        
                        if(isset($item['id']) && (int)$item['id']){
                            $exp = ExpensesManager::findOne((int)$item['id']);
                            if ($exp->id === NULL)
                                throw new HttpException(404, 'App Not Exist');
                        }else{
                            $exp = new ExpensesManager;
                        }
                        
                        $exp->manager_id = (int)$item['manager_id'];
                        $exp->date = $autotruck->date;
                        $exp->cost = round($item['cost'],2);
                        $exp->autotruck_id = $autotruck->id;
                        $exp->comment = trim(strip_tags($item['comment']));

                        $exp->save();

                    }
                }
                

                //Добавление наименовании
                if(isset($post['App']) && count($post['App']) && $autotruck->id){
                    
                    foreach ($post['App'] as $key => $item) {
                        
                        if(isset($item['id']) && (int)$item['id']){
                            
                            $a = App::findOne((int)$item['id']);
                            
                            if ($a->id === NULL)
                                throw new HttpException(404, 'App Not Exist');
                        }else{
                            $a = new App;
                        }

                        $a->client = ($item['client'])?(int)$item['client']:0;
                        $a->info = $item['info'];
                        if((int)$item['type']){
                            $a->weight = 1;
                            $a->type = 1;
                        }else{
                            $a->weight = ($item['weight']) ? $item['weight'] : 0;
                            $a->type = 0;
                        }
                        $a->rate = ($item['rate']) ? round($item['rate'],2) : 0;
                        
                        $a->comment = $item['comment'];
                        $a->autotruck_id = $autotruck->id;

                        if($a->save()){
                            
                            Yii::$app->session->setFlash("AppSaved");   
                        }else{
                            Yii::$app->session->setFlash("AppSavedError");
                        }
                    }
                }
                Yii::$app->session->setFlash("AutotruckUpdated");

                $autotruck->sendNotification(); 

            }else{
                Yii::$app->session->setFlash("AutotruckUpdatedError");
            }

            

            Yii::$app->response->redirect(array('autotruck/read', 'id' => $autotruck->id,'query'=>$query));
        }

        //$this->layout = "/main";
    
        return $this->render('update', array(
            'autotruck' => $autotruck,'listAutotruck'=>$listAutotruck
        ));

    }

	
	public function actionStatusRemove(){

        $get = Yii::$app->request->get();

        if(isset($get['id']) && (int)$get['id']){
            $at = AppTrace::findOne((int)$get['id']);

            if(isset($at->trace_id)){
                $at->delete();
                Yii::$app->session->setFlash("success","Статус удален!");
            }else{
                Yii::$app->session->setFlash("danger","Статус не найден!");
            }
        }else{
            Yii::$app->session->setFlash("danger","Статус не найден!");
        }

        if(isset($get['aid']) && (int)$get['aid']){
            return Yii::$app->response->redirect(array("autotruck/read",'id'=>(int)$get['aid']));
        }else{
            return Yii::$app->response->redirect(array("autotruck/index"));
        }
        
    }




	public function actionDelete($id = NULL){

		if($id == NULL){
			Yii::$app->session->setFlash("autotruckDeleteError");
			return Yii::$app->response->redirect(array("autotruck/index"));
		}

		$Autotruck = Autotruck::findOne($id);

		if($Autotruck === NULL){
			Yii::$app->session->setFlash("autotruckDeleteError");
			return Yii::$app->response->redirect(array("autotruck/index"));
		}

        try {
            //#sverka_restart
            //Перед тем как удалить получим пользователей, чтоб поосле удаления перерасчитать сверку
            $sql = "SELECT DISTINCT c.`user_id`  
                FROM app as a
                INNER JOIN client as c ON a.client = c.id
                WHERE a.autotruck_id = {$Autotruck->id}";
            $users = \Yii::$app->db->createCommand($sql)->queryAll();

            
            $Autotruck->delete();

            foreach ($users as $u_id) {
                if(isset($u_id['user_id'])){
                    User::refreshUserSverka($u_id['user_id']); 
                }
            }   
        } catch (Exception $e) {
            
        }

		Yii::$app->session->setFlash("autotruckDeleted");
		return Yii::$app->response->redirect(array("autotruck/index"));
	}




    public function actionDeleteAll(){
        $Autotruck = Autotruck::find()->all();

        foreach ($Autotruck as $key => $a) {
           $a->delete();
        }

        Yii::$app->session->setFlash("autotrucksDeleted");
        return Yii::$app->response->redirect(array("autotruck/index"));
    }



}