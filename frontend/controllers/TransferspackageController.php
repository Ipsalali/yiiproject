<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\UploadedFile;
use common\models\TransfersPackage;
use common\models\Transfer;
use common\models\Seller;
use common\models\Client;
use common\models\SellerExpenses;
use frontend\modules\TransferspackageFilter;


class TransferspackageController extends Controller{


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
                        'actions' => ['index', 'error','create','read','show-files','download','unlinkfile','change-status','story-status','get-row-service','get-row-expenses','delete','remove-transfer-ajax','remove-expenses-ajax'],
                        'allow' => true,
                        'roles' => ['transferspackage'],
                    ],
                ],
            ]
        ];
    }



	public function actionIndex(){

		$modelFilters = new TransferspackageFilter;
		$dataProvider = $modelFilters->search(Yii::$app->request->queryParams);
		
		return $this->render('index',array('modelFilters'=>$modelFilters,'dataProvider'=>$dataProvider));
	}



	public function actionCreate($id = null){
		
		
		$post = Yii::$app->request->post();
		
		if($id !== null){

			$model = TransfersPackage::findOne($id);

			if(!isset($model->id))
				throw new HttpException(404,'Document Does Not Exist');

		}elseif(isset($post['package_id']) && (int)$post['package_id']){
		    
		    $model = TransfersPackage::findOne((int)$post['package_id']);

			if(!isset($model->id))
				throw new HttpException(404,'Document Does Not Exist');
				
		}else{
			$model = new TransfersPackage();
		}

		

		$transfers = $model->transfers;
		$expenses = $model->sellerExpenses;
		$sellers = Seller::getSellers();
		$clients = Client::find()->All();

		if(isset($post['TransfersPackage'])){


			if(isset($model->id)){
					$model->tempFiles = $model->files;
			}

			if($model->load($post) && $model->validate()){

				$model->files = UploadedFile::getInstances($model, 'files');
				if ($model->files) {
					if($fName = $model->uploadFile()){
						$model->files = $fName;
					}else{
						Yii::$app->session->setFlash("warning",'Файлы не удалось загрузить на сервер!');
					}
	            }

	            $typeAction = isset($model->id) && $model->id  ? 2 : 4;
	            
	            $model->setStoryAttributeTypeAction($typeAction);
	            $error = false;
	            if($model->save(1)){
                    Yii::$app->session->setFlash("success",'Перевод сохранен');
                    
                    
	            	//Добавление наименования
	            	if(isset($post['Transfer']) && count($post['Transfer'])){
	            		
	            		$res = $model->saveTransfers($post['Transfer']);
	            		if($res === true){
	            			Yii::$app->session->setFlash("success",'Перевод и услуги сохранены');
	            			//Перезагружаем услуги, если расходы будут с ошибками, их нужно отправить на клиент обратно
	            			$transfers = $model->transfers;
	            		}elseif(is_array($res) && count($res)){
	            			Yii::$app->session->setFlash("danger",'Услуги не сохранены, не правильный формат данных!');
	            			$transfers = $res;
							$error = true;
	            		}elseif($res === 2){
	            			Yii::$app->session->setFlash("warning",'Не удалось добавить все услуги, при добавлении некоторых услуг, произошла ошибка!');
	            		}elseif($res === false){
	            			Yii::$app->session->setFlash("warning",'Услуги не найдены!');
	            		}

	            		
	            	}
	            	
	            	//Добавление расход
	            	if(isset($post['SellerExpenses']) && count($post['SellerExpenses'])){
	            	    
	            	    $res = $model->saveExpenses($post['SellerExpenses']);
	            		if($res === true){
	            			Yii::$app->session->setFlash("success",'Перевод,услуги и расходы сохранены');
	            			//Перезагружаем расходы, если услуги были с ошибками, их нужно отправить на клиент обратно
		                    $expenses = $model->sellerExpenses;
	            		}elseif(is_array($res) && count($res)){
	            			Yii::$app->session->setFlash("danger",'Расходы не сохранены, не правильный формат данных!');
	            			$expenses = $res;
							$error = true;
	            		}elseif($res === 2){
	            			Yii::$app->session->setFlash("warning",'Не удалось добавить все расходы, при добавлении некоторых расход, произошла ошибка!');
	            		}elseif($res === false){
	            			Yii::$app->session->setFlash("warning",'Расходы не найдены!');
	            		}
	            	    
	            	}
	            	
	            	if(!$error){
	            	    return $this->redirect(["transferspackage/index"]);
	            	}else{
	            	    return $this->render('form',['model'=>$model,'transfers'=>$transfers,'sellers'=>$sellers,'clients'=>$clients,'expenses'=>$expenses]);
	            	}
	            	

	            }else{
	            	Yii::$app->session->setFlash("danger",'Не удалось сохранить коллекцию переводов!');
	            	
	            }

			}else{

				//Чтоб не потерять заполненные даные услуг, передадим их обратно клиенту
				$transfers = isset($post['Transfer']) ? $post['Transfer'] : [];
				return $this->render('form',['model'=>$model,'transfers'=>$transfers,'sellers'=>$sellers,'clients'=>$clients,'expenses'=>$expenses]);
			}

		}

		return $this->render('form',['model'=>$model,'transfers'=>$transfers,'sellers'=>$sellers,'clients'=>$clients,'expenses'=>$expenses]);
	}







	public function actionRead($id = NULL){

		
		if($id == null)
			throw new HttpException(404,'Not Found!');

		$model = TransfersPackage::findOne($id);

		if($model === NULL)
			throw new HttpException(404,'Document Does Not Exist');

		return $this->render('read',array("model"=>$model));

	}






	public function actionShowFiles($id){
		if(Yii::$app->request->isAjax){

	        if($id == NULL){
	        	$model = null;
			}else{
				$model = TransfersPackage::findOne((int)$id);
			}
	        
	        return $this->renderAjax("files",['model'=>$model]);
        }else{
        	return $this->redirect(["transferspackage/index"]);
        }
	}







	public function actionStoryStatus($id){
		if(Yii::$app->request->isAjax){

	        if($id == NULL){
	        	$model = null;
			}else{
				$model = TransfersPackage::findOne((int)$id);
			}
	        
	        return $this->renderAjax("storyStatus",['model'=>$model]);
        }else{
        	return $this->redirect(["transferspackage/index"]);
        }
	}













	public function actionChangeStatus($id){

		if($id == null)
			throw new HttpException(404,'Not Found!');

		$model = TransfersPackage::findOne($id);

		if(!isset($model->id))
			throw new HttpException(404,'Document Does Not Exist');

    	if(Yii::$app->request->post() || Yii::$app->request->isAjax){

	        
	        if(Yii::$app->request->post()){
	            if($model->load(Yii::$app->request->post()) && $model->validate()){
	                
	                $model->setStoryAttributeTypeAction(4);
	                if($model->save(1)){
	                    Yii::$app->session->setFlash('success',"Статус перевода #{$model->id} изменен!");
	                }else{
	                    Yii::$app->session->setFlash('warning',"Статус перевода не изменен");
	                }
	            }else{
	                Yii::$app->session->setFlash('danger','Ошибка при изменении статуса перевода!');
	            }

	            return $this->redirect(["transferspackage/read",'id'=>$model->id]);
	        }

	        return $this->renderAjax("statusForm",['model'=>$model]);
        }else{
        	return $this->redirect(["transferspackage/index"]);
        }
    }












	public function actionDelete($id = NULL){

		if($id == NULL){
			Yii::$app->session->setFlash("danger","Переданы не корректные данные");
			return Yii::$app->response->redirect(array("transferspackage/index"));
		}

		$model = TransfersPackage::findOne($id);

		if(!isset($model->id) || $model === NULL){
			Yii::$app->session->setFlash("danger","Перевод не найден");
			return Yii::$app->response->redirect(array("transferspackage/index"));
		}

		$model->delete();

		Yii::$app->session->setFlash("success","Перевод {$model->name} перенесен в архив");
		return Yii::$app->response->redirect(array("transferspackage/index"));
	}



	

	public function actionRemoveTransferAjax(){

		if(Yii::$app->request->isAjax){

			$get = Yii::$app->request->get();

			$answer = array();

			if((int)$get['id']){
			
				$id = (int)$get['id'];

				$t = Transfer::findOne($id);
				if($t){
					$answer['result']  = $t->delete();
				}else{
					$answer['error']['text'] = 'not found transfer';
				}
			}else{
				$answer['result'] = 0;
			}
		
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			
			return $answer;
		}
	}
	
	
	
	public function actionRemoveExpensesAjax(){

		if(Yii::$app->request->isAjax){

			$get = Yii::$app->request->get();

			$answer = array();

			if((int)$get['id']){
			
				$id = (int)$get['id'];

				$t = SellerExpenses::findOne($id);
				if($t){
					$answer['result']  = $t->delete();
				}else{
					$answer['error']['text'] = 'not found expenses';
				}
			}else{
				$answer['result'] = 0;
			}
		
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
			
			return $answer;
		}
	}






	public function actionUnlinkfile($id=null,$file = null){
		if($id == null || $file == null)
			throw new HttpException(404, 'Not Found');

		$model = TransfersPackage::findOne((int)$id);

		if($model === null){
			throw new HttpException(404, 'Not Found');
		}


		$model->unlinkFile($file);
		
		Yii::$app->session->setFlash("success","Файл {$file} удален!");

		return Yii::$app->response->redirect(array("transferspackage/read",'id'=>$model->id));
	}










	public function actionDownload($id=null,$file = null){
		if($id == null || $file == null)
			throw new HttpException(404, 'Not Found');

		$model = TransfersPackage::findOne((int)$id);

		if($model === null){
			throw new HttpException(404, 'Not Found');
		}

		if($model->fileExists($file) && file_exists($model::$filesPath.$file)){
			Yii::$app->response->SendFile($model::$filesPath.$file)->send();
		}else{
			Yii::$app->session->setFlash("danger","Файл {$file} не найден");
			return Yii::$app->response->redirect(array("transferspackage/read",'id'=>$model->id));
		}
	}






	public function actionGetRowService(){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$get = \Yii::$app->request->get();

		$model = new Transfer(); 
		$clients = Client::find()->All();
		$n = isset($get['n']) ? (int)$get['n'] : 0;

		$ans['html'] = $this->renderPartial("rowService",['model'=>$model,'clients'=>$clients,'n'=>$n]);
		return $ans;
	}
	
	
	
	public function actionGetRowExpenses(){
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

		$get = \Yii::$app->request->get();

		$model = new SellerExpenses(); 
		$sellers = Seller::getSellers();
		$n = isset($get['n']) ? (int)$get['n'] : 0;

		$ans['html'] = $this->renderPartial("rowExpenses",['model'=>$model,'sellers'=>$sellers,'n'=>$n]);
		return $ans;
	}



}