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
use common\models\User;
use common\helper\EDateTime;
use frontend\modules\ListAction;
use frontend\modules\AutotruckSearch;
use frontend\modules\AutotruckReport;
use yii\data\ActiveDataProvider;
use frontend\models\ExpensesManager;
use yii\data\ArrayDataProvider;
use frontend\helpers\ExcelAutotruck;
use yii\web\UploadedFile;

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
                        'actions' => ['create'],
                        'allow' => true,
                        'roles' => ['autotruck/create'],
                    ],
                    [
                        'actions' => ['read', 'to-excel','download','autotruck-story','app-story','expenses-story'],
                        'allow' => true,
                        'roles' => ['autotruck/read'],
                    ],
                    [
                        'actions' => ['update','unlinkfile','download', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/update'],
                    ],
                    [
                        'actions' => ['delete', 'index'],
                        'allow' => true,
                        'roles' => ['autotruck/delete'],
                    ],
                    [
                        'actions' => ['removeappajax', 'set-out-stock','set-all-out-stock','index'],
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
                    ],
                    [
                    	'actions'=>['addown'],
                    	'allow' => true,
                    	'roles'=>['autotruck/create']
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
		$autotruck->scenario = Autotruck::SCENARIO_CREATE;
		$app = new App;

		$filters = (array_key_exists('filters', $_GET)) ? $_GET['filters'] : array();
		Yii::$app->view->params['filters'] = $filters;

		$post = Yii::$app->request->post();

		$eDate = new EDateTime;
        
        
        
		if(isset($post['Autotruck'])){
			$autotruck->name = $post['Autotruck']['name'];
			$autotruck->course = ($post['Autotruck']['course'])?round($post['Autotruck']['course'],4):0;
			$autotruck->country = ($post['Autotruck']['country'])? $post['Autotruck']['country']:0;
			$autotruck->date = ($post['Autotruck']['date'])?date('Y-m-d',strtotime($post['Autotruck']['date'])):date("Y-m-d");
			$autotruck->description = $post['Autotruck']['description'];
			$autotruck->status = $post['Autotruck']['status']? $post['Autotruck']['status']: 0;

			$autotruck->auto_number = strip_tags($post['Autotruck']['auto_number']);
			$autotruck->auto_name = strip_tags($post['Autotruck']['auto_name']);
			$autotruck->gtd = strip_tags($post['Autotruck']['gtd']);
			$autotruck->decor = strip_tags($post['Autotruck']['decor']);

			if($_FILES['Autotruck']['name']['file'][0]){
				$autotruck->file = UploadedFile::getInstances($autotruck, 'file');
				if ($autotruck->file && $fName = $autotruck->uploadFile()) {
	                $autotruck->file = $fName;
	            }else{
	            	Yii::$app->session->setFlash("FileUploadError");	
	            }
        	}

			if($autotruck->save(1)){
			    
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
						$exp->date = isset($item['date']) && $item['date'] ? date("Y-m-d",strtotime($item['date'])) : $autotruck->date;
						$exp->manager_id = (int)$item['manager_id'];
						$exp->cost = round($item['cost'],2);
						$exp->autotruck_id = $autotruck->id;
						$exp->comment = trim(strip_tags($item['comment']));

						if($exp->save(1)){
							//обновление сверки
							try {
								User::refreshUserSverka($exp->manager_id);
							} catch (Exception $e) {}
						}
					}
				}

				//Добавление наименовании
				if(isset($post['App']) && count($post['App']) && $autotruck->id){

					foreach ($post['App'] as $key => $item) {
						
						$a = new App;
						$a->client = ($item['client'])?(int)$item['client']:0;
						
						$a->sender = isset($item['sender'])?(int)$item['sender']:null;
						$a->package = isset($item['package'])?(int)$item['package']:null;
						$a->count_place = isset($item['count_place']) ? (int)$item['count_place'] : null;
						
						$a->info = $item['info'];
						$a->info = $item['info'];
						if((int)$item['type']){
							$a->weight = 1;
							$a->type = 1;
						}else{
							$a->weight = ($item['weight']) ? $item['weight'] : 0;
							$a->type = 0;
						}

						$a->summa_us = ($item['summa_us']) ? round($item['summa_us'],2) : 0;
						$a->rate = ($item['rate']) ? round($item['rate'],2) : 0;
						$a->comment = $item['comment'];
						$a->autotruck_id = $autotruck->id;

						$a->save(1);
					}
							
					
					
				}else{
					if($autotruck->id){
						return Yii::$app->response->redirect(array("autotruck/read",'id'=>$autotruck->id));
					}
					return Yii::$app->response->redirect(array("autotruck/create"));
				}

                if($autotruck->status){
				    $autotruck->sendNotification();
                }

                //Временно реализуем перерасчет сверки
		        if($autotruck->activeStatus->send_check){
		            //обновление сверки
		            try {
		                $autotruck->refreshClientsSverka();
		            } catch (Exception $e) {}
		        }

		        Yii::$app->session->setFlash("AutotruckSaved");

                if(Yii::$app->user->can("clientExtended")){
					Yii::$app->response->redirect(array("client/profile"));
				}
				else{
					Yii::$app->response->redirect(array("autotruck/index"));
				}

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









	public function actionToExcel($id = null){
		if($id == null)
			throw new HttpException(404,'Not Found!');

		$autotruck = Autotruck::findOne($id);

		if($autotruck === NULL)
			throw new HttpException(404,'Document Does Not Exist');

		$ExcelAutotruck = new ExcelAutotruck();
		
		
		$file = $ExcelAutotruck->export($autotruck);

        if(file_exists($file)){
            Yii::$app->response->SendFile($file)->send();
        }
		else{
            Yii::$app->response->redirect(array("autotruck/read",'id'=>$id));
        }
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

		if(isset($post['Autotruck'])){

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
        	
        	$autotruck->auto_number = strip_tags($post['Autotruck']['auto_number']);
			$autotruck->auto_name = strip_tags($post['Autotruck']['auto_name']);
			$autotruck->gtd = strip_tags($post['Autotruck']['gtd']);
			$autotruck->decor = strip_tags($post['Autotruck']['decor']);
			
			$autotruck->description = $post['Autotruck']['description'];
			
			if($_FILES['Autotruck']['name']['file'][0]){

				$autotruck->tempFiles = $autotruck->file;
				$autotruck->file = UploadedFile::getInstances($autotruck, 'file');
				
	            if ($autotruck->file && $fName = $autotruck->uploadFile()) {
	                $autotruck->file = $fName;
	            }else{
	            	Yii::$app->session->setFlash("FileUploadError");	
	            }
        	}
			

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
				}elseif($autotruck->id && $autotruck->activeStatusTrace->trace_id){

					$apptrace = AppTrace::findOne($autotruck->activeStatusTrace->trace_id);
					$apptrace->trace_date = ($post['Autotruck']['date_status'])
									? date('Y-m-d',strtotime($post['Autotruck']['date_status'])):date("Y-m-d");

					$apptrace->save();
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
						
						$exp->date = isset($item['date']) && $item['date'] ? date("Y-m-d",strtotime($item['date'])) : $autotruck->date;
						$exp->cost = round($item['cost'],2);
						$exp->autotruck_id = $autotruck->id;
						$exp->comment = trim(strip_tags($item['comment']));

						if($exp->save(1)){
							//обновление сверки
							try {
								User::refreshUserSverka($exp->manager_id);
							} catch (Exception $e) {}
						}
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

						$a->client = isset($item['client'])?(int)$item['client']:0;

						$a->sender = isset($item['sender'])?(int)$item['sender']:0;
						$a->package = isset($item['package'])?(int)$item['package']:0;
						$a->count_place = isset($item['count_place'])? (int)$item['count_place'] : 0;
						
						$a->info = isset($item['info']) ? $item['info'] : "";
						if((int)$item['type']){
							$a->weight = 1;
							$a->type = 1;
						}else{
							$a->weight = isset($item['weight']) ? $item['weight'] : 0;
							$a->type = 0;
						}
						$a->rate = isset($item['rate']) ? round($item['rate'],2) : 0;
						
						$a->summa_us = isset($item['summa_us']) ? round($item['summa_us'],2) : 0;
						$a->comment = $item['comment'];
						$a->autotruck_id = $autotruck->id;

						if($a->save(1)){
							
							Yii::$app->session->setFlash("AppSaved");	
						}else{
							Yii::$app->session->setFlash("AppSavedError");
						}
					}
				}
				Yii::$app->session->setFlash("AutotruckUpdated");

				if($autotruck->status){
				    $autotruck->sendNotification();
                }

                //Временно реализуем перерасчет сверки
                if($autotruck->activeStatus->send_check){
                    //обновление сверки
                    try {
                        $autotruck->refreshClientsSverka();
                    } catch (Exception $e) {}
                }

			}else{
				Yii::$app->session->setFlash("AutotruckUpdatedError");
			}

			
			if(Yii::$app->user->can("clientExtended")){
					Yii::$app->response->redirect(array("client/profile"));
			}
			else{
				return Yii::$app->response->redirect(array('autotruck/read', 'id' => $autotruck->id,'query'=>$query));
			}
			
    	}

    	//$this->layout = "/main";
    
    	return $this->render('update', array(
        	'autotruck' => $autotruck,'listAutotruck'=>$listAutotruck
    	));

	}









	public function actionDelete($id = NULL){

		if($id == NULL){
			Yii::$app->session->setFlash("PostDeleteError");
			return Yii::$app->response->redirect(array("autotruck/index"));
		}

		$autotruck = Autotruck::findOne($id);

		if($autotruck === NULL){
			Yii::$app->session->setFlash("PostDeleteError");
			return Yii::$app->response->redirect(array("autotruck/index"));
		}

		try {
			//#sverka_restart
			//Перед тем как удалить получим пользователей, чтоб поосле удаления перерасчитать сверку
			$sql = "SELECT DISTINCT c.`user_id`  
                FROM app as a
                INNER JOIN client as c ON a.client = c.id
                WHERE a.autotruck_id = {$autotruck->id}";
	        $users = \Yii::$app->db->createCommand($sql)->queryAll();

			$autotruck->delete();

			foreach ($users as $u_id) {
				if(isset($u_id['user_id'])){
	            	User::refreshUserSverka($u_id['user_id']);	
				}
	        }	
		} catch (Exception $e) {
			
		}
		

		Yii::$app->session->setFlash("PostDeleted");
		return Yii::$app->response->redirect(array("autotruck/index"));
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

					//#sverka restart
					$client = $app->client ? $app->buyer : null;

					$app->delete();

					if($client && isset($client->user_id)){
						try {
							User::refreshUserSverka($client->user_id);
						} catch (Exception $e) {
							
						}
					}

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

					//#sverka restart
					$manager_id = $exp->manager_id ? $exp->manager_id : null;
					$exp->delete();
					if($manager_id){
						try {
							User::refreshUserSverka($manager_id);
						} catch (Exception $e) {
							
						}
					}

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

			if($model->save(1)){
				Yii::$app->session->setFlash("ExpensesManagerAddSuccess");

				//#sverka restart
				try {
					User::refreshUserSverka($model->manager_id);
				} catch (Exception $e) {}

			}else{
				Yii::$app->session->setFlash("ExpensesManagerAddError");
			}
			Yii::$app->response->redirect(array('autotruck/read', 'id' => (int)$post['ExpensesManager']['autotruck_id']));
		}else{
			Yii::$app->response->redirect(array("autotruck/index"));
		}
	}










	public function actionReport(){


		$autotruckReport = new AutotruckReport;


		$params = Yii::$app->request->queryParams;
		$dataProvider = $autotruckReport->search($params);

		return $this->render('report', array(
        	'dataProvider' => $dataProvider,
        	'autotruckReport'=>$autotruckReport,
    	));
	}


	











	public function actionUnlinkfile($id=null,$file = null){
		if($id == null || $file == null)
			throw new HttpException(404, 'Not Found');

		$autotruck = Autotruck::findOne((int)$id);

		if($autotruck === null){
			throw new HttpException(404, 'Not Found');
		}


		$autotruck->unlinkFile($file);

		return Yii::$app->response->redirect(array("autotruck/read",'id'=>$autotruck->id));
	}










	public function actionDownload($id=null,$file = null){
		if($id == null || $file == null)
			throw new HttpException(404, 'Not Found');

		$autotruck = Autotruck::findOne((int)$id);

		if($autotruck === null){
			throw new HttpException(404, 'Not Found');
		}

		if($autotruck->fileExists($file) && file_exists('uploads/'.$file)){
			Yii::$app->response->SendFile('uploads/'.$file)->send();
		}else{
			Yii::$app->session->setFlash("NotFoundedFile");
			return Yii::$app->response->redirect(array("autotruck/read",'id'=>$autotruck->id));
		}

	}





	public function actionSetOutStock(){
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$get = Yii::$app->request->get();
		$answer['result'] = 0;
		if(isset($get['id']) && (int)$get['id']){
			$App = App::findOne((int)$get['id']);
			if(isset($App->id) && $App->id){
				$App->out_stock = (isset($get['value']) && (int)$get['value']) ? 1 : 0;
				if($App->save()){
					$answer['result'] = 1;
				}
			}
		}
		return $answer;
	}




	public function actionSetAllOutStock(){
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$get = Yii::$app->request->get();
		$answer['result'] = 0;
		if(isset($get['id']) && (int)$get['id']){
			$Autotruck = Autotruck::findOne((int)$get['id']);
			if(isset($Autotruck->id) && $Autotruck->id){
				
				$value = (isset($get['value']) && (int)$get['value']) ? 1 : 0;
				
				$answer['result'] = 1;$Autotruck->setAllAppOutStock($value);
				
				
			}
		}
		return $answer;
	}


	public function actionAutotruckStory($id){
        if(Yii::$app->request->isAjax){

            if($id == NULL){
                $model = null;
            }else{
                $model = Autotruck::findOne((int)$id);
            }
            
            return $this->renderAjax("story",['model'=>$model]);
        }else{
            return $this->redirect(["autotruck/index"]);
        }
    }


    public function actionAppStory($id){
        if(Yii::$app->request->isAjax){

            if($id == NULL){
                $model = null;
            }else{
                $model = App::findOne((int)$id);
            }
            
            return $this->renderAjax("storyApp",['model'=>$model]);
        }else{
            return $this->redirect(["autotruck/index"]);
        }
    }


    public function actionExpensesStory($id){
        if(Yii::$app->request->isAjax){

            if($id == NULL){
                $model = null;
            }else{
                $model = ExpensesManager::findOne((int)$id);
            }
            
            return $this->renderAjax("storyExpenses",['model'=>$model]);
        }else{
            return $this->redirect(["autotruck/index"]);
        }
    }


}