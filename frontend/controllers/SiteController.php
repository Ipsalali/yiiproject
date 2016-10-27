<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\Pagination;
use common\models\Post;
use common\models\Client;
use frontend\models\App;
use frontend\models\Autotruck;
use common\models\User;
use frontend\models\PaymentsExpenses;
use frontend\models\ExpensesManager;


/**
 * Site controller
 */
class SiteController extends Controller
{   

    public $layout = "main";

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['search'],
                        'allow' => true,
                        'roles' => ['admin','manager'],
                    ],
                    [
                        'actions' => ['sverka'],
                        'allow' => true,
                        'roles' => ['site/sverka'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions'=>['addpaymentsmanager'],
                        'allow' => true,
                        'roles' => ['admin','manager'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {   
        //Если пользователь не авторизован отправляем на страницу авторизации
        if (Yii::$app->user->isGuest) return $this->actionLogin();
        
        if(Yii::$app->user->identity->role->name !="client"){
            $query = Post::find();
            $countPost = clone $query;
            $pages = new Pagination(['totalCount'=>$countPost->count(), 'pageSize' => 6]);
       
            $post = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id'=>SORT_DESC])->all();
            return $this->render('index', [
                'post' => $post,
                'pages' => $pages,
            ]);
        }elseif(Yii::$app->user->identity->role->name == "client"){
            return $this->render('clientindex');
        }

        
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {   

        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
           
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {

                //Устанавливаем для пользователя роль
                $userRole = Yii::$app->authManager->getRole('client');
                
                Yii::$app->authManager->assign($userRole, $user->getId());

                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }


    public function actionSearch(){

        if(Yii::$app->request->isAjax){

            $post = Yii::$app->request->post();

            $answer = array();

            if($post['keywords']){

                $keywords = trim(strip_tags($post['keywords']));
                
                
                $autotrucks = Autotruck::searchByKey($keywords);
                $apps = App::searchByKey($keywords);
                $clients = Client::searchByKey($keywords);

                $this->layout = "/site/empty";

                if(count($apps) || count($autotrucks) || count($clients)){
                    $html = $this->render('search', [
                        'autotrucks' => $autotrucks,
                        'apps' => $apps,
                        'clients' => $clients,
                    ]);
                    $answer['result'] = 1;
                }else{
                    $answer['result'] = 0;
                    $html = "No results";
                }

                $answer['result'] = 1;
                $answer['html'] = $html;
                $answer['keywords'] = $keywords;

            }else{
                $answer['result'] = 0;
            }
        
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            return $answer;
        }
    }


    public function actionSverka(){

        $params = Yii::$app->request->get();
        $expenses = array();
        $data_params = array();
        $manager = new User;
        if($params && (int)$params['manager']){
            $data_params['date_from'] = $params['date_from'];
            $data_params['date_to'] = $params['date_to'];
            $data_params['manager'] = (int)$params['manager'];
            $manager = User::findOne($data_params['manager']);
            $expenses = $manager->getExpenses($data_params['date_from'],$data_params['date_to']);
            $payments = $manager->getPayments($data_params['date_from'],$data_params['date_to']);
            $sverka = $manager->getPaymentsAndExpenses($data_params['date_from'],$data_params['date_to']);
        }
        return $this->render('sverka', ["sverka"=>$sverka,"manager"=>$manager,"expenses"=>$expenses,"data_params"=>$data_params,'payments'=>$payments]);
    }


    public function actionAddpaymentsmanager(){

        $answer = array();
        $answer['result'] = 0;

        if(Yii::$app->request->isAjax){
            $post = Yii::$app->request->post();
            $answer['data'] = $post;
            if($post['PaymentsExpenses'] && count($post['PaymentsExpenses'])){
                
                foreach ($post['PaymentsExpenses'] as $key => $item) {
                    if(isset($item['id']) && (int)$item['id']){

                        $pm = PaymentsExpenses::findOne((int)$item['id']);
                        if ($pm->id === NULL){
                            $answer['messages'][] = "Оплата с суммой ".$item['sum'].' не добавлена';
                            continue;
                        }
                    }else{
                       $pm = new PaymentsExpenses;
                       $pm->date = ($item['date']) ? date("Y-m-d",strtotime($item['date'])) :date("Y-m-d"); 
                       $pm->manager_id = (int)$item['manager_id'];
                    }
                     
                    $pm->sum = round(trim(strip_tags($item['sum'])),2);
                    $pm->comment = trim(strip_tags($item['comment']));
                    if($pm->save()){
                        $answer['messages'][] = "Оплата с суммой ".$item['sum'].' добавлена';
                        $answer['result'] = 1;
                    }else{
                        $answer['messages'][] = "Оплата с суммой ".$item['sum'].' не добавлена';
                    }
                } 
            }

            //Редактирование если есть расход
            if($post['ExpensesManager'] && count($post['ExpensesManager'])){
                
                foreach ($post['ExpensesManager'] as $key => $item) {
                    if(isset($item['id']) && (int)$item['id']){

                        $em = ExpensesManager::findOne((int)$item['id']);
                        if ($em->id === NULL){
                            $answer['messages'][] = "Оплата с суммой ".$item['sum'].' не добавлена';
                            continue;
                        }

                        $em->cost = round(trim(strip_tags($item['sum'])),2);
                        $em->comment = trim(strip_tags($item['comment']));
                        if($em->save())
                            $answer['result'] = 1;
                    }
                     
                } 
            }  
        }
        
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
        return $answer;
    }


    public function actionRemovepayajax(){
        if(Yii::$app->request->isAjax){

            $post = Yii::$app->request->post();

            $answer = array();

            if($post['id']){
            
                $id = (int)$post['id'];

                $exp = PaymentsExpenses::findOne($id);
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
}
