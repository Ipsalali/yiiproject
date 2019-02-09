<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\UploadedFile;

use frontend\models\Autotruck;
use frontend\models\App;
use frontend\models\AppTrace;
use frontend\models\ExpensesManager;
use frontend\modules\AutotruckSearch;
use frontend\modules\AutotruckReport;
use frontend\helpers\ExcelAutotruck;

use common\models\SupplierCountry;
use common\models\Sender;
use common\models\TypePackaging;
use common\models\Client;
use common\models\Status;
use common\models\User;

class AutotruckController extends Controller{

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
                        'actions' => ['create','addown','form','get-row-exp','get-row-app'],
                        'allow' => true,
                        'roles' => ['autotruck/create'],
                    ],
                    [
                        'actions' => ['read', 'to-excel','download','autotruck-story','app-story','expenses-story'],
                        'allow' => true,
                        'roles' => ['autotruck/read'],
                    ],
                    [
                        'actions' => ['update','form','get-row-exp','get-row-app','unlinkfile','download','removeappajax', 'set-out-stock','set-all-out-stock'],
                        'allow' => true,
                        'roles' => ['autotruck/update'],
                    ],
                    [
                        'actions' => ['delete'],
                        'allow' => true,
                        'roles' => ['autotruck/delete'],
                    ],
                    [
                        'actions' => ['addexpenses','removeexpajax'],
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
		
		return $this->render('index',array('autotruckSearch'=>$autotruckSearch,'dataProvider'=>$dataProvider));
	}



	public function actionForm($id = null){
		
		$post = Yii::$app->request->post();
		
		if($id !== null){
			$autotruck = Autotruck::findOne($id);
			if(!isset($autotruck->id))
        		throw new HttpException(404, 'Заявка не найдена!');
		}elseif(isset($post['autotruck_id']) && (int)$post['autotruck_id']){
		    
		    $autotruck = Autotruck::findOne((int)$post['autotruck_id']);
			if(!isset($autotruck->id))
				throw new HttpException(404,'Заявка не найдена!');
				
		}else{
			$autotruck = new Autotruck();
			$autotruck->scenario = Autotruck::SCENARIO_CREATE;
		}

		$expenses = $autotruck->getExpensesManager();
		$apps= $autotruck->getApps();

		if(isset($post['Autotruck'])){

       		if($autotruck->load($post) && $autotruck->validate()){
	        	$has_new_status = ($autotruck->status == $post['Autotruck']['status'])? false : true;
	        	$prev_status = $autotruck->status;
	        	$trace_date = ($post['Autotruck']['date_status'])
									? date('Y-m-d\TH:i:s',strtotime($post['Autotruck']['date_status'])):date("Y-m-d\TH:i:s");
       		
				if(isset($_FILES['Autotruck']['name']['file'][0]) && $_FILES['Autotruck']['name']['file'][0]){
					$autotruck->tempFiles = $autotruck->file;
					$autotruck->file = UploadedFile::getInstances($autotruck, 'file');
		            if ($autotruck->file && $fName = $autotruck->uploadFile()) {
		                $autotruck->file = $fName;
		            }else{
		            	Yii::$app->session->setFlash("warning","Не удалось загузить файл");	
		            }
	        	}

	        	$error = false;
	        	if($autotruck->save(1)){
	        		Yii::$app->session->setFlash("success",'Заявкавка сохранена');
	        		// Статус заявки
					$params['autotruck_id'] = $autotruck->id;
					$params['status_id'] = $autotruck->status;
					$params['prevstatus_id'] = 0;
					$params['trace_date'] = $trace_date;
					
					if($autotruck->id && $has_new_status){
						$params['prevstatus_id'] =($has_new_status)? $prev_status : 0;
						$trace = AppTrace::addSelf($params);
					}elseif($autotruck->id && $autotruck->status){
						$activeStatusTrace = $autotruck->activeStatusTrace;
						if($activeStatusTrace instanceof AppTrace && isset($activeStatusTrace->trace_id) && $activeStatusTrace->trace_id){
							$apptrace = $activeStatusTrace;
							$apptrace->trace_date = $trace_date;
							$apptrace->save();
						}else{
							$params['traсe_first'] = 1;
							$trace = AppTrace::addSelf($params);
						}
					}

	        		//Добавление наименования
	            	if(isset($post['App']) && count($post['App'])){
	            		
	            		$res = $autotruck->saveApps($post['App']);
	            		if($res === true){
	            			Yii::$app->session->setFlash("success",'Наименования и услуги сохранены');
	            			//Перезагружаем услуги, на случай если расходы будут с ошибками, их нужно отправить на клиент обратно
	            			$apps = $autotruck->getApps();
	            		}elseif(is_array($res) && count($res)){
	            			Yii::$app->session->setFlash("danger",'Наименования и услуги не сохранены, не правильный формат данных!');
	            			$apps = $res;
							$error = true;
	            		}elseif($res === 2){
	            			Yii::$app->session->setFlash("warning",'Не удалось добавить наименования, при добавлении некоторых наименований и услуг, произошла ошибка!');
	            		}elseif($res === false){
	            			Yii::$app->session->setFlash("warning",'Услуги не найдены!');
	            		}
	            	}

	            	//Добавление расходов
	            	if(isset($post['ExpensesManager']) && count($post['ExpensesManager'])){
	            	    
	            	    $res = $autotruck->saveExpenses($post['ExpensesManager']);
	            		if($res === true){
	            			Yii::$app->session->setFlash("success",'Наименования,услуги и расходы сохранены');
	            			//Перезагружаем расходы, если услуги были с ошибками, их нужно отправить на клиент обратно
		                    $expenses = $autotruck->getExpensesManager();
	            		}elseif(is_array($res) && count($res)){
	            			Yii::$app->session->setFlash("danger",'Расходы не сохранены, не правильный формат данных!');
	            			$expenses = $res;
							$error = true;
	            		}elseif($res === 2){
	            			Yii::$app->session->setFlash("warning",'Не удалось добавить расходы, при добавлении некоторых расходов, произошла ошибка!');
	            		}elseif($res === false){
	            			Yii::$app->session->setFlash("warning",'Расходы не найдены!');
	            		}
	            	}

	            	if(!$error){

	            		if($autotruck->status){
						    $autotruck->sendNotification();
		                }

		                //Временно реализуем перерасчет сверки
		                if($autotruck->needToSendCheck){
		                    //обновление сверки
		                    try {
		                        $autotruck->refreshClientsSverka();
		                        $autotruck->stateToExport();
		                        \common\modules\ExportAutotruck::export($autotruck);
		                    } catch (Exception $e) {}
		                }

		                if(Yii::$app->user->can("clientextended")){
							return Yii::$app->response->redirect(["client/profile"]);
						}else{
							return $this->redirect(['autotruck/read', 'id' => $autotruck->id]);
						}
	            	    
	            	}else{
	            	    return $this->render('form',['autotruck'=>$autotruck,'apps'=>$apps,'expenses'=>$expenses]);
	            	}

	        	}else{
	            	Yii::$app->session->setFlash("danger",'Не удалось сохранить заявку!');
	            }

       		}else{
       			//Чтоб не потерять заполненные даные услуг, передадим их обратно клиенту
				$apps = isset($post['App']) ? $post['App'] : [];
				return $this->render('form',['autotruck'=>$autotruck,'apps'=>$apps,'expenses'=>$expenses]);
       		}
    	}

		
    	return $this->render('form',[
        	'autotruck' => $autotruck,
        	'expenses'=>$expenses,
        	'apps'=>$apps
    	]);
	}



	public function actionGetRowExp(){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$get = \Yii::$app->request->get();

		$model = new ExpensesManager(); 
		$expManagers = User::getSellers();
		$n = isset($get['n']) ? (int)$get['n'] : 0;

		$ans['html'] = $this->renderPartial("rowExp",[
												'model'=>$model,
												'expManagers'=>$expManagers,
												'n'=>$n
											]);
		return $ans;
	}


	public function actionGetRowApp(){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$get = \Yii::$app->request->get();
		
		$senders = Sender::find()->orderBy(['name'=>'DESC'])->all();
		$packages = TypePackaging::find()->all();
		
		$user = \Yii::$app->user->identity;
		$userIsClientExtended = \Yii::$app->user->can("clientExtended");
		$clients = ($userIsClientExtended) ? [$user->client] : Client::find()->where(['isDeleted'=>0])->orderBy(['name'=>'DESC'])->all();

		$model = new App();
		$n = isset($get['n']) ? (int)$get['n'] : 0;
		$type = isset($get['type']) && (int)$get['type'] ? 1 : 0;

		$ans['html'] = $this->renderPartial("rowApp",[
											'model'=>$model,
											'senders'=>$senders,
											'packages'=>$packages,
											'clients'=>$clients,
											'n'=>$n,
											'type'=>$type
										]);
		return $ans;
	}




	public function actionRead($id = NULL){

		if($id == null)
			throw new HttpException(404,'Not Found!');

		$autotruck = Autotruck::findOne($id);

		if($autotruck === NULL)
			throw new HttpException(404,'Document Does Not Exist');

		$get = Yii::$app->request->get();

		if(isset($get['cause']) && (int)$get['cause'] == 403){
			
			$user_id = isset($get['user_id']) && $get['user_id'] ? (int)$get['user_id'] : 0;

			if($user_id == Yii::$app->user->id){
				$note = "Данный документ у вас уже открыт на редактирование!!!";
			}else{
				$u = User::findOne($user_id);
				$note = isset($u->id) ? "В данный момент этот документ редактирует  пользователь ".$u->name."!!!" : "В данный момент этот документ редактирует другой пользователь";
			}

			Yii::$app->session->setFlash("warning",$note);
		}

		$autotruck->getGtdDate();
		return $this->render('read',array("autotruck"=>$autotruck));
	}





	public function actionToExcel($id = null){
		if($id == null)
			throw new HttpException(404,'Заявка не найдена');

		$autotruck = Autotruck::findOne($id);

		if($autotruck === NULL)
			throw new HttpException(404,'Заявка не найдена');

		$ExcelAutotruck = new ExcelAutotruck();
		
		$file = $ExcelAutotruck->export($autotruck);

        if(file_exists($file))
            return Yii::$app->response->SendFile($file)->send();
        
        return Yii::$app->response->redirect(array("autotruck/read",'id'=>$id));
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