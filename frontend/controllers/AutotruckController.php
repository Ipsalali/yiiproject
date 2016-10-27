<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Post;
use yii\web\HttpException;
use frontend\models\Autotruck;
use frontend\models\App;
use frontend\models\AppTrace;
use common\models\Client;
use common\models\Status;
use common\helper\EDateTime;
use frontend\modules\ListAction;
use frontend\modules\AutotruckSearch;
use yii\data\ActiveDataProvider;
use frontend\models\ExpensesManager;
use yii\data\ArrayDataProvider;

class AutotruckController extends Controller{

	public $layout = "main.php";

	public function actions()
    {
        return [
            // 'search' => [
            //     'class' => ListAction::className(),
            //     'filterModel' => new AutotruckSearch(),
            //     'directPopulating' => true,
            //     'view'=>'@frontend/views/autotruck/index'
            // ],
            // 'index' => [
            //     'class' => ListAction::className(),
            //     'filterModel' => new AutotruckSearch(),
            //     'directPopulating' => true,
            //     'view'=>'@frontend/views/autotruck/index'
            // ]
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
                        'actions' => ['update', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/update'],
                    ],
                    [
                        'actions' => ['delete', 'index'],
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
                    	'roles'=>['autotruck/addexpenses']
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

	public function actionCreate(){
		$autotruck = new Autotruck;
		$app = new App;

		$filters = (array_key_exists('filters', $_GET)) ? $_GET['filters'] : array();
		Yii::$app->view->params['filters'] = $filters;

		$post = Yii::$app->request->post();

		$eDate = new EDateTime;

		if(isset($post['Autotruck'])){
			$autotruck->name = $post['Autotruck']['name'];
			$autotruck->course = ($post['Autotruck']['course'])?round($post['Autotruck']['course'],2):0;
			$autotruck->country = ($post['Autotruck']['country'])? $post['Autotruck']['country']:0;
			$autotruck->date = ($post['Autotruck']['date'])?date('Y-m-d',strtotime($post['Autotruck']['date'])):date("Y-m-d");
			$autotruck->description = $post['Autotruck']['description'];
			$autotruck->status = $post['Autotruck']['status']?$post['Autotruck']['status']:0;

			if($autotruck->save()){
				//Добавление статуса
				if($autotruck->id && $autotruck->status){
					$apptrace = new AppTrace;
					$apptrace->autotruck_id = $autotruck->id;
					$apptrace->status_id = $autotruck->status;
					$apptrace->traсe_first = 1;
					$apptrace->traсe_last = 1;
					$apptrace->prevstatus_id = 0;
					$apptrace->trace_date  = ($post['Autotruck']['date_status'])
									? date('Y-m-d',strtotime($post['Autotruck']['date_status'])):$autotruck->date;

					$apptrace->save();		
				}

				//Добавление расхода
				if(isset($post['ExpensesManager']) && count($post['ExpensesManager']) && $autotruck->id){
					foreach ($post['ExpensesManager'] as $key => $item) {
						$exp = new ExpensesManager;
						$exp->date = $autotruck->date;
						$exp->manager_id = (int)$item['manager_id'];
						$exp->cost = round($item['cost'],2);
						$exp->autotruck_id = $autotruck->id;
						$exp->comment = trim(strip_tags($item['comment']));

						$exp->save();
					}
				}

				//Добавление наименовании
				if(isset($post['App']) && count($post['App']) && $autotruck->id){

					foreach ($post['App'] as $key => $item) {
						
						$a = new App;
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

						$a->save();
					}
							
					
					Yii::$app->session->setFlash("AutotruckSaved");
					Yii::$app->response->redirect(array("autotruck/index"));

				}else{
					Yii::$app->session->setFlash("AutotruckEmpty");
					Yii::$app->response->redirect(array("autotruck/create"));
				}

				Yii::$app->response->redirect(array('autotruck/index'));

				$autotruck->sendNotification();

			}else{
				Yii::$app->session->setFlash("AutotruckCreateError");
				Yii::$app->response->redirect(array("autotruck/create"));
			} 
		}

		$query = $autotruck->find()->orderBy(['id'=>SORT_DESC]);
		Yii::$app->view->params['query'] = $query;
		return $this->render('create',array('autotruck'=>$autotruck,'app'=>$app));
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
			$autotruck->course = ($post['Autotruck']['course'])?round($post['Autotruck']['course'],2):0;
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
			

			if($autotruck->save()){
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

	public function actionDelete($id = NULL){

		if($id == NULL){
			Yii::$app->session->setFlash("PostDeleteError");
			Yii::$app->response->redirect(array("post/index"));
		}

		$post = Post::findOne($id);

		if($post === NULL){
			Yii::$app->session->setFlash("PostDeleteError");
			Yii::$app->response->redirect(array("post/index"));
		}

		$post->delete();

		Yii::$app->session->setFlash("PostDeleted");
		Yii::$app->response->redirect(array("post/index"));
	}

	public function actionRemoveappajax(){

		if(Yii::$app->request->isAjax){

			$post = Yii::$app->request->post();

			$answer = array();

			if($post['id']){
			
				$id = (int)$post['id'];

				$app = App::findOne($id);
				if($app){
					$answer['result'] = (int)$post['id'];

					$app->delete();

				}else{
					$answer['error']['text'] = 'not found app';
				}
			}else{
				$answer['result'] = 0;
			}
		
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			
			return $answer;
		}
	}


	public function actionRemoveexpajax(){

		if(Yii::$app->request->isAjax){

			$post = Yii::$app->request->post();

			$answer = array();

			if($post['id']){
			
				$id = (int)$post['id'];

				$exp = ExpensesManager::findOne($id);
				if($exp){
					$answer['result'] = (int)$post['id'];

					$exp->delete();

				}else{
					$answer['error']['text'] = 'not found app';
				}
			}else{
				$answer['result'] = 0;
			}
		
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			
			return $answer;
		}
	}

	public function actionAddexpenses(){
		$post = Yii::$app->request->post();

		if($post['ExpensesManager'] && (int)$post['ExpensesManager']['manager_id'] && $post['ExpensesManager']['cost']
			&& $post['ExpensesManager']['autotruck_id']){
			$model = new ExpensesManager;
			$model->manager_id = (int)$post['ExpensesManager']['manager_id'];
			$model->cost =round($post['ExpensesManager']['cost'],2);
			$model->autotruck_id = (int)$post['ExpensesManager']['autotruck_id'];
			$model->comment = trim(strip_tags($post['ExpensesManager']['comment']));

			if($model->save()){
				Yii::$app->session->setFlash("ExpensesManagerAddSuccess");
			}else{
				Yii::$app->session->setFlash("ExpensesManagerAddError");
			}
			Yii::$app->response->redirect(array('autotruck/read', 'id' => (int)$post['ExpensesManager']['autotruck_id']));
		}else{
			Yii::$app->response->redirect(array("autotruck/index"));
		}
	}

	public function actionReport(){
		$report = Autotruck::getReport();

		$dataProvider = new ArrayDataProvider([
        		'key'=>'id',
        		'allModels' => $report,
        		'sort' => [
            		'attributes' => ['id', 'name','course','country','weight','sum_us','sum_ru','expenses'],
        		],
        		'pagination' => [
        			'pageSize' => 10,
    			],
		]);

		return $this->render('report', array(
        	'dataProvider' => $dataProvider
    	));
	}

}