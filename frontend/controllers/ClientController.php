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
                        'actions' => ['create', 'index'],
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

		$get = Yii::$app->request->get();
		$filters = (array_key_exists('filters', $get)) ? $get['filters'] : array();

		$client = new Client;
		if(count($filters)){
			$data = $client->findByFilter($filters);
		}else{
			$data = $client->find()->all();
		}

		return $this->render('index',array('data'=>$data,'filters'=>$filters));
	}

	public function actionCreate(){
		$client = new Client;


		$user = new SignupForm();

		$managers = User::getManagers();

		$post = Yii::$app->request->post();

		if(isset($post['Client'])){
            $client->full_name = trim(strip_tags($post['Client']['full_name']));
			$client->name = trim(strip_tags($post['Client']['name']));
            $client->contract_number = trim(strip_tags($post['Client']['contract_number']));
			$client->description = trim(strip_tags($post['Client']['description']));
			$client->phone = trim(strip_tags($post['Client']['phone']));
			$client->client_category_id = ($_POST['Client']['client_category_id'])? $_POST['Client']['client_category_id']:0;
 			$client->manager = ($_POST['Client']['manager'])? $_POST['Client']['manager']:0;
            $client->payment_clearing = ($_POST['Client']['payment_clearing'])? $_POST['Client']['payment_clearing']:0;
			if($client->save()){
				
				if ($user->load($post)) {

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


		return $this->render('create',array('data'=>array("client"=>$client,"user"=>$user),"mode"=>"create",'managers'=>$managers));
	}

	public function actionRead($id = NULL){

		if($id == null)
			throw new HttpException(404,'Not Found!');

		$client = Client::findOne($id);

		if($client === NULL)
			throw new HttpException(404,'Document Does Not Exist');

		$autotrucks = $client->appsSortAutotruck;
		$grafik = $client->getDataForGrafik();

		return $this->render('read',array("client"=>$client,'autotrucks'=>$autotrucks,'grafik'=>$grafik));

	}



	public function actionUpdate($id = null){
		if($id == null)
			throw new HttpException(404, 'Not Found');

		$Client = Client::findOne($id);
		
		$managers = User::getManagers();

		if ($Client === NULL)
        	throw new HttpException(404, 'Document Does Not Exist');
		
		if (isset($_POST['Client']))
    	{  
            $Client->full_name = trim(strip_tags($_POST['Client']['full_name']));
        	$Client->name = trim(strip_tags($_POST['Client']['name']));
            $Client->contract_number = trim(strip_tags($_POST['Client']['contract_number']));
        	$Client->description = trim(strip_tags($_POST['Client']['description']));
 			$Client->phone = trim(strip_tags($_POST['Client']['phone']));
 			$Client->client_category_id=($_POST['Client']['client_category_id'])?$_POST['Client']['client_category_id']:0;
 			$Client->manager = ($_POST['Client']['manager'])?$_POST['Client']['manager']:0;
            $Client->payment_clearing = ($_POST['Client']['payment_clearing'])? $_POST['Client']['payment_clearing']:0;
        	if ($Client->save()){

        		if($_POST['User']['email'])
        			$user = $Client->user;
        			$user->email = trim(strip_tags($_POST['User']['email']));
        			$user->save();
            	Yii::$app->response->redirect(array('client/index'));
        	}
    	}

    	return $this->render('create', array(
        	'data'=>array("client"=>$Client),'managers'=>$managers,
        	"mode"=>"update"
    	));

	}



	public function actionDelete($id = NULL){

		if($id == NULL){
			Yii::$app->session->setFlash("ClientDeleteError");
			Yii::$app->response->redirect(array("post/index"));
		}

		$client = Client::findOne($id);

		if($client === NULL){
			Yii::$app->session->setFlash("ClientDeleteError");
			Yii::$app->response->redirect(array("post/index"));
		}

		$profile = User::findOne($client->user_id);
		if(is_object($profile)){
			$profile->delete();

		}
		
		$client->delete();

		

		Yii::$app->session->setFlash("ClientDeleted");
		Yii::$app->response->redirect(array("client/index"));
	}

	public function actionApp($id = null){
		if($id == NULL){
			throw new HttpException(404, 'Not Found');
		}

		$client = Client::findOne($id);

		if($client === NULL){
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

		if($client_model === NULL){
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

        if($client_model === NULL){
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

		if($client_model === NULL){
			throw new HttpException(404,'Document Does Not Exist');
		}

		if($autotruck_model === NULL){
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

        if($client === NULL)
            throw new HttpException(404,'Document Does Not Exist');

        $autotrucks = $client->appsSortAutotruck;

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