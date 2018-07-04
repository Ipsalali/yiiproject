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
	
	public function actions()
    {
        // return [
        //     'index' => [
        //         'class' => ListAction::className(),
        //         'filterModel' => new ClientSearch(),
        //         'directPopulating' => true,
        //         'view'=>'@frontend/views/client/index'
        //     ]
        // ];
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
                        'roles' => ['client/index'],
                    ],
                    [
                        'actions' => ['create', 'index','get-relation'],
                        'allow' => true,
                        'roles' => ['client/create'],
                    ],
                    [
                        'actions' => ['read', 'index'],
                        'allow' => true,
                        'roles' => ['client/read'],
                    ],
                    [
                        'actions' => ['update', 'index'],
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
                    	'actions'=>['profile','index'],
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

	public $layout = "/client/client";






	public function actionIndex(){

        $clientSearch = new ClientSearch;
		$dataProvider = $clientSearch->search(Yii::$app->request->queryParams);
		return $this->render('index',array('dataProvider'=>$dataProvider,'clientSearch'=>$clientSearch));
	}










	public function actionCreate(){
		$client = new Client;

		$user = new SignupForm();

		$managers = User::getManagers();

		$post = Yii::$app->request->post();
        $error = false;

		if(isset($post['Client'])){
            
            

            if(isset($post['use_free_client']) && (int)$post['use_free_client']){
                if(isset($post['Client']['user_id']) && !(int)$post['Client']['user_id']){
                    $error = true;
                }
            }


			if($client->load($post) && !$error && $client->save()){
				
                //Сохраняем связь между организацией и клиентом
                $c_n = isset($post['Client']['contract_number']) ? trim(strip_tags($post['Client']['contract_number'])) : "";


                ClientOrganisation::saveRelation($client,$c_n);

				if (!$client->user_id && $user->load($post)) {

            		if ($profile = $user->signup()) {

                		//Устанавливаем для пользователя роль
                		$userRole = Yii::$app->authManager->getRole('client');
                
                		Yii::$app->authManager->assign($userRole, $profile->getId());

                		$client->user_id = $profile->getId();

                		$client->save();

            		}
        		
        		}

				Yii::$app->response->redirect(array("client/index"));
			} 

		}

        $freeUser = User::getUnAssignedUserForClient();
        $mode_user_create = true;

		return $this->render('create',array("error"=>$error,"freeUser"=>$freeUser,"client"=>$client,"user"=>$user,"mode"=>"create",'managers'=>$managers,"mode_user_create"=>$mode_user_create));
	}




	public function actionUpdate($id = null){
		if($id == null)
			throw new HttpException(404, 'Not Found');

		$Client = Client::findOne($id);

		$managers = User::getManagers();

        $user = new SignupForm();
        
		if(!isset($Client->id))
        	throw new HttpException(404, 'Document Does Not Exist');
		

        $error = false;
        
        $post = Yii::$app->request->post();

		if (isset($_POST['Client']))
    	{   


            if(isset($post['use_free_client']) && (int)$post['use_free_client']){
                if(isset($post['Client']['user_id']) && !(int)$post['Client']['user_id']){
                    $error = true;
                }
            }

        	if ($Client->load($_POST) && $Client->save()){

                $c_n = isset($_POST['Client']['contract_number']) ? trim(strip_tags($_POST['Client']['contract_number'])) : "";

                
                //Сохраняем связь между организацией и клиентом
                ClientOrganisation::saveRelation($Client,$c_n);

                
                if (!isset($Client->user->id) && isset($post['SignupForm']) &&  $user->load($post)) {

                    if ($profile = $user->signup()) {

                        //Устанавливаем для пользователя роль
                        $userRole = Yii::$app->authManager->getRole('client');
                
                        Yii::$app->authManager->assign($userRole, $profile->getId());

                        $Client->user_id = $profile->getId();

                        $Client->save();

                    }
                
                }elseif(isset($_POST['User']['email']) && $_POST['User']['email']){
                    $user = $Client->user;
                    $user->email = trim(strip_tags($_POST['User']['email']));
                    $user->save();
                }
        			
            	

                Yii::$app->response->redirect(array('client/read','id'=>$Client->id));
        	}
    	}

        
        if($Client->user){
            $user = $Client->user;
            $mode_user_create = false;
        }else{
            
            $mode_user_create = true;
        }

        $freeUser = User::getUnAssignedUserForClient();

    	return $this->render('create', array(
        	"client"=>$Client,'managers'=>$managers,
        	"mode"=>"update",'user'=>$user,"mode_user_create"=>$mode_user_create,"freeUser"=>$freeUser,"error"=>$error
    	));

	}



    public function actionRead($id = NULL){

        if($id == null)
            throw new HttpException(404,'Not Found!');

        $client = Client::findOne($id);
        
        if(!isset($client->id))
            throw new HttpException(404,'Document Does Not Exist');

        $autotrucks = $client->appsSortAutotruck;
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

		$profile = User::findOne($client->user_id);
		if(is_object($profile)){
			$profile->delete();

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

		//print_r($autotrucks);
		//exit;
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
			Yii::$app->response->redirect(array("client/index"));
		//print_r($apps);
		$activeStatus = $autotruck_model->activeStatus;
        $activeTrace = $autotruck_model->activeStatusTrace;
		//print_r($activeStatus->notification_template);
		// print_r($client_model);
		// exit;
		$this->layout = "/client/empty";
		$mail_template =  $this->render('notification_status', array(
        	'apps' => $apps,'activeStatus'=>$activeStatus,"activeTrace"=>$activeTrace,"autotruck_model"=>$autotruck_model
    	));

       
		$mail = 'web-ali@yandex.ru';

    	$res = $this->MailSend($mail,$mail_template);

    	if($res){
    		Yii::$app->session->setFlash("Notification_sended");
    	}else{
    		Yii::$app->session->setFlash("Notification_error");
    	}

    	Yii::$app->response->redirect(array("client/app",'id'=>$client));
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

        $autotrucks = $client->getAppsSortAutotruck($client->ownApps);

        return $this->render('profile',array("client"=>$client,'autotrucks'=>$autotrucks));
    }


    public function actionAutotruckpayment(){
        $post = Yii::$app->request->post();
        $get = Yii::$app->request->get();
        $cPayment = new CustomerPayment;
        
        if($get && (int)$get['id']){
            $cPayment = CustomerPayment::findOne((int)$get['id']);
        }

        if($post){
            if((int)$post['CustomerPayment']['payment_state_id'] && (int)$post['CustomerPayment']['client_id'] && (int)$post['CustomerPayment']['autotruck_id']){
                $cPayment->autotruck_id = (int)$post['CustomerPayment']['autotruck_id'];
                $cPayment->client_id = (int)$post['CustomerPayment']['client_id'];
                $cPayment->payment_state_id = (int)$post['CustomerPayment']['payment_state_id'];
                $cPayment->sum = round($post['CustomerPayment']['sum'],2);
                $cPayment->comment = trim(strip_tags($post['CustomerPayment']['comment']));

                if($cPayment->save()){
                    Yii::$app->session->setFlash("CustomerPaymentSuccess");
                    Yii::$app->response->redirect(array("client/read",'id'=>(int)$post['CustomerPayment']['client_id']));
                }else{
                    Yii::$app->session->setFlash("CustomerPaymentError");
                    Yii::$app->response->redirect(array("client/read",'id'=>(int)$post['CustomerPayment']['client_id']));
                }
                
            
            }elseif((int)$post['CustomerPayment']['client_id']){
                Yii::$app->session->setFlash("Not data for payment");
                Yii::$app->response->redirect(array("client/read",'id'=>(int)$post['CustomerPayment']['client_id']));
            }else{
               Yii::$app->response->redirect(array("client/index")); 
            }
        }else{
          Yii::$app->response->redirect(array("client/index"));
        }
    }
}