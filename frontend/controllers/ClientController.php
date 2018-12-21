<?php

namespace frontend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use common\models\Client;
use common\models\User;
use yii\web\HttpException;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\helpers\Checkexcel;
use frontend\helpers\Mail;
use frontend\models\Autotruck;
use frontend\modules\ListAction;
use frontend\modules\ClientSearch;
use frontend\models\CustomerPayment;
use common\models\ClientOrganisation;


class ClientController extends Controller{
	
	
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
                        'roles' => ['client/index'],
                    ],
                    [
                        'actions' => ['form', 'index','get-relation'],
                        'allow' => true,
                        'roles' => ['client/create'],
                    ],
                    [
                        'actions' => ['read', 'index','client-story'],
                        'allow' => true,
                        'roles' => ['client/read'],
                    ],
                    [
                        'actions' => ['form', 'index'],
                        'allow' => true,
                        'roles' => ['client/update'],
                    ],
                    [
                        'actions' => ['delete', 'index'],
                        'allow' => true,
                        'roles' => ['client/delete'],
                    ],
                    [
                        'actions' => ['app', 'index'],
                        'allow' => true,
                        'roles' => ['client/app'],
                    ],
                    [
                    	'actions'=>['sendnotification','index'],
                    	'allow' => true,
                        'roles' => ['client/sendnotification'],
                    ],
                    [
                    	'actions'=>['check','index'],
                    	'allow' => true,
                        'roles' => ['client/check'],
                    ],
                    [
                        'actions'=>['mycheck','index'],
                        'allow' => true,
                        'roles' => ['client/mycheck'],
                    ],
                    [
                    	'actions'=>['profile'],
                    	'allow' => true,
                        'roles' => ['client/profile'],
                    ],
                    [
                        'actions'=>['autotruckpayment','index'],
                        'allow' => true,
                        'roles' => ['client/autotruckpayment'],
                    ]
                ],
            ]
        ];
    }




	public function actionIndex(){
        $clientSearch = new ClientSearch;
		$dataProvider = $clientSearch->search(Yii::$app->request->queryParams);
		return $this->render('index',array('dataProvider'=>$dataProvider,'clientSearch'=>$clientSearch));
	}





	public function actionForm($id = null){
        $post = Yii::$app->request->post();

        if($id || isset($post['model_id'])){
           $id = isset($post['model_id']) ? (int)$post['model_id'] : (int)$id;

           $model =  Client::findOne(['id'=>$id]);
           if(!isset($model->id))
                throw new \Exception("Клиент не найден!",404); 
        }else{
           $model = new Client(); 
        }

		$user = new SignupForm();
		$managers = User::getManagers();
        $error = false;

		if(isset($post['Client'])){
            
            if(isset($post['use_free_client']) && (int)$post['use_free_client']){
                if(isset($post['Client']['user_id']) && !(int)$post['Client']['user_id']){
                    $error = true;
                }
            }

			if($model->load($post) && !$error && $model->save(1)){
				
                Yii::$app->session->setFlash('success',"Данные клиента сохранены");

                //Сохраняем связь между организацией и клиентом
                $c_n = isset($post['Client']['contract_number']) ? trim(strip_tags($post['Client']['contract_number'])) : "";

                ClientOrganisation::saveRelation($model,$c_n);


                //Экспорт в 1С
                \common\modules\ExportClient::export($model);

				if (!$model->user_id && $user->load($post)) {

            		if ($profile = $user->signup()) {

                		//Устанавливаем для пользователя роль
                		$userRole = Yii::$app->authManager->getRole('client');
                
                		Yii::$app->authManager->assign($userRole, $profile->getId());

                		$model->user_id = $profile->getId();

                		$model->save(1);

                        Yii::$app->session->setFlash('success',"Данные профиля сохранены");
                        return Yii::$app->response->redirect(["client/index"]);
            		}
        		}elseif(isset($post['User'])){
                    $user = $model->user;
                    if($user->load($post) && $user->save(true)){

                        Yii::$app->session->setFlash('success',"Данные профиля сохранены");
                        return Yii::$app->response->redirect(["client/index"]);
                    }
                    
                    if(!isset($post['use_free_client'])){
                        Yii::$app->session->setFlash('error',"Данные профиля не сохранены");
                    }
                }

                
			}else{
                Yii::$app->session->setFlash('error',"Данные клиента не сохранены");
            }
		}

        $freeUser = User::getUnAssignedUserForClient();
        if($model->user_id){
            $user = $model->user;
            $mode_user_create = false;
        }else{
            $mode_user_create = true;
        }

		return $this->render('form',[
            "error"=>$error,
            "freeUser"=>$freeUser,
            "model"=>$model,
            "user"=>$user,
            'managers'=>$managers,
            "mode_user_create"=>$mode_user_create
        ]);
	}





    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $client = Client::findOne($id);
        
        if(!isset($client->id))
            throw new HttpException(404,'Document Does Not Exist');
        $autotrucks = $client->getAutotrucksWithApps();
        // $autotrucks = $client->appsSortAutotruck;
        
        $grafik = $client->getDataForGrafik();

        return $this->render('read',array("client"=>$client,'autotrucks'=>$autotrucks,'grafik'=>$grafik));

    }


    public function actionGetRelation(){
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $post = Yii::$app->request->post();
        $answer['result'] = 0;
        $answer['value'] = "";
        if(isset($post['client_id']) && (int)$post['client_id'] && isset($post['org_id']) && (int)$post['org_id']){
            $cl = ClientOrganisation::findByClienAndOrg((int)$post['client_id'],(int)$post['org_id']);

            if(isset($cl->client_id) && $cl->client_id){
                $answer['result'] = 1;
                $answer['value'] = $cl->relation_number;
            }
        }
        return $answer;
    }






	public function actionDelete($id = NULL){

		if($id == NULL){
			Yii::$app->session->setFlash("ClientDeleteError");
			return Yii::$app->response->redirect(array("post/index"));
		}

		$client = Client::findOne($id);

		if(!isset($client->id)){
			Yii::$app->session->setFlash("ClientDeleteError");
			return Yii::$app->response->redirect(array("post/index"));
		}

        if($client->user_id){
            $profile = User::findOne($client->user_id);
            if(is_object($profile)){
                $profile->delete();
            }
        }
		
		
		$client->delete();

		

		Yii::$app->session->setFlash("ClientDeleted");
		return Yii::$app->response->redirect(array("client/index"));
	}






    






	public function actionApp($id = null){
		if($id == NULL){
			throw new HttpException(404, 'Not Found');
		}

		$client = Client::findOne($id);

		if(!isset($client->id)){
			throw new HttpException(404,'Document Does Not Exist');
		}

		$autotrucks = $client->appsSortAutotruck;

		return $this->render('apps', array(
        	'autotrucks'=>$autotrucks,"client"=>$client
    	));
	}














	public function actionCheck($client,$autotruck){
		
		if($client == NULL){
			throw new HttpException(404, 'Not Found');
		}

		if($autotruck == NULL){
			throw new HttpException(404, 'Not Found');
		}

		$client_model = Client::findOne($client);
        
        if(!isset($client_model->id)){
			throw new HttpException(404,'Document Does Not Exist');
		}

		$apps = $client_model->getAutotruckApps($autotruck);
        	
        if(!count($apps))
			Yii::$app->response->redirect(array("client/index"));

        $checkexcel = new Checkexcel();
		$file = $checkexcel->generateCheck($apps,$client_model);

        if(file_exists($file)){
            Yii::$app->response->SendFile($file)->send();
        }
		else{
            Yii::$app->response->redirect(array("client/read",'id'=>$client));
        }
		
	}











    public function actionMycheck($autotruck){
        
        if(Yii::$app->user->isGuest){
            Yii::$app->response->redirect(array("client/profile"));
        }

        $client_model = Yii::$app->user->identity->client;

        if($autotruck == NULL){
            throw new HttpException(404, 'Not Found');
        }

        if(!isset($client_model->id)){
            throw new HttpException(404,'Document Does Not Exist');
        }

        $apps = $client_model->getAutotruckApps($autotruck);
            
        if(!count($apps))
            Yii::$app->response->redirect(array("client/index"));

        $checkexcel = new Checkexcel();
        $file = $checkexcel->generateCheck($apps,$client_model);

        if(file_exists($file)){
            Yii::$app->response->SendFile($file)->send();
        }
        else{
            Yii::$app->response->redirect(array("client/profile"));
        }
        
    }














	public function actionSendnotification($client,$autotruck){
		if($client == NULL){
			throw new HttpException(404, 'Not Found');
		}

		if($autotruck == NULL){
			throw new HttpException(404, 'Not Found');
		}

		$client_model = Client::findOne($client);
		$autotruck_model =Autotruck::findOne($autotruck);

		if(!isset($client_model->id)){
			throw new HttpException(404,'Document Does Not Exist');
		}

		if(!isset($autotruck_model->id)){
			throw new HttpException(404,'Document Does Not Exist');
		}

		$apps = $client_model->getAutotruckApps($autotruck);
		if(!count($apps))
			return Yii::$app->response->redirect(["client/index"]);
		
        
		$activeStatus = $autotruck_model->activeStatus;
        $activeTrace = $autotruck_model->activeStatusTrace;
		

		$this->layout = "empty";
		
        $mail_template =  $this->render('notification_status', array(
        	'apps' => $apps,'activeStatus'=>$activeStatus,"activeTrace"=>$activeTrace,"autotruck_model"=>$autotruck_model
    	));

       
		$mail = 'web-ali@yandex.ru';

    	$res = $this->MailSend($mail,$mail_template);

    	if($res){
    		Yii::$app->session->setFlash("success",'Уведомление отправлено.');
    	}else{
    		Yii::$app->session->setFlash("danger",'Не удолось отправить уведомление.');
    	}

    	return Yii::$app->response->redirect(["client/app",'id'=>$client]);
	}



	function MailSend($tomail,$html,$files=null){
            
            $sender="Notification";
            $subject="Notification";
            $text="Notification";

            $mail = new Mail();

            $mail->protocol = 'mail';
            $mail->parameter = '';
            $mail->hostname = '';
            $mail->username = '';
            $mail->password = '';
            $mail->port = 25;
            $mail->timeout = 5;
            
            //$mail->setTo($this->config->get('config_email'));
            //$mail->setTo('web-ali@yandex.ru');
            
           

            $mail->setTo($tomail);
            $mail->setFrom("magomedaliev.93@mail.ru");
            $mail->setSender($sender);
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($html);
            $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            $mail->setAttachment($files);
            $mail->send();
            
            return 1;//$mail->send();
    }


    public function actionProfile(){

        if(Yii::$app->user->identity->id == null)
            throw new HttpException(404,'Not Found!');

        $client = Yii::$app->user->identity->client;

        if(!isset($client->id))
            throw new HttpException(404,'Document Does Not Exist');

        $autotrucks = $client->getOwnAutotrucksWithApps();

        return $this->render('profile',["client"=>$client,'autotrucks'=>$autotrucks]);
    }



    public function actionClientStory($id){
        if(Yii::$app->request->isAjax){

            if($id == NULL){
                $model = null;
            }else{
                $model = Client::findOne((int)$id);
            }
            
            return $this->renderAjax("story",['model'=>$model]);
        }else{
            return $this->redirect(["client/index"]);
        }
    }
}