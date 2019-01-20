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
use common\models\Organisation;
use frontend\models\PaymentsExpenses;
use frontend\models\ExpensesManager;
use frontend\modules\PaymentsExpensesReport;
use common\models\Spender;
use common\models\Sender;
use common\models\UserAction;

/**
 * Site controller
 */
class SiteController extends Controller
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
                        'actions' => ['index','search','logout','register-event','remove-event'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions'=>['org-report'],
                        'allow'=>true,
                        'roles'=>['autotruck/report']
                    ],
                    
                    [
                        'actions' => ['login','reset-password','request-password-reset'],
                        'allow' => true,
                        'roles' => ['?','@'],
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
            ]
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
        if(Yii::$app->user->isGuest) return $this->actionLogin();
        
        if(Yii::$app->user->identity->role->name !="client"){
            
            if(Yii::$app->user->can('autotruck/index')){
                return Yii::$app->response->redirect(['autotruck/index']);
                
            }elseif(Yii::$app->user->can('client/index')){
                return Yii::$app->response->redirect(['client/index']);
            }

            $query = Post::find();
            $countPost = clone $query;
            $pages = new Pagination(['totalCount'=>$countPost->count(), 'pageSize' => 6]);
       
            $post = $query->offset($pages->offset)->limit($pages->limit)->orderBy(['id'=>SORT_DESC])->all();
            return $this->render('index', [
                'post' => $post,
                'pages' => $pages,
            ]);
        }elseif(Yii::$app->user->identity->role->name == "client"){
            
            Yii::$app->response->redirect(['client/profile']);
            
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
        $this->layout = "login";
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
        $this->layout = "login";
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Ссылка для восстановления пароля отправлена на ваш электронный адрес. Не забудьте проверить папку \"спам\".');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Извините, мы не смогли отправить на ваш электронный адрес ссылку для восстановления пароля.');
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
        $this->layout = "login";
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
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $get = Yii::$app->request->get();

            
            if(isset($get['keywords'])){

                $keywords = trim(strip_tags($get['keywords']));
                
                
                $autotrucks = Autotruck::searchByKey($keywords);
                $apps = App::searchByKey($keywords);
                $clients = Client::searchByKey($keywords);
                $sender = Sender::searchByKey($keywords);

                $this->layout = "site/empty";

                if(count($apps) || count($autotrucks) || count($clients) || count($sender)){
                    $html = $this->render('search', [
                        'autotrucks' => $autotrucks,
                        'apps' => $apps,
                        'clients' => $clients,
                        'sender' => $sender,
                    ]);
                    $answer['result'] = 1;
                }else{
                    $answer['result'] = 0;
                    $html = "No results";
                }

                $answer['html'] = $html;
                $answer['keywords'] = $keywords;

            }else{
                $answer['result'] = 0;
            }
            
            return $answer;
        }else{
            return $this->goHome();
        }
    }










    

    public function actionExpensesPeopleByKey(){

        $post = Yii::$app->request->post();

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if(isset($post['key']) && trim(strip_tags($post['key'])) != ""){
            $key = trim(strip_tags($post['key']));

            $expM = User::getExpensesManagers($key);
        }else{
            $expM = User::getExpensesManagers();
        }
        return ['result'=>1,'managers'=>$expM];
    }










    public function actionAddpaymentsmanager(){
    
        
        $answer = array();
        $answer['result'] = 0;
        $manager = null;
        if(Yii::$app->request->isAjax){
            
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
            $post = Yii::$app->request->post();
            $answer['data'] = $post;
            if(isset($post['PaymentsExpenses']) && $post['PaymentsExpenses'] && count($post['PaymentsExpenses'])){
                
                foreach ($post['PaymentsExpenses'] as $key => $item) {
                    if(isset($item['id']) && (int)$item['id']){

                        $pm = PaymentsExpenses::findOne((int)$item['id']);
                        if ($pm->id === NULL){
                            $answer['messages'][] = "Оплата с суммой ".$item['sum'].' не добавлена';
                            continue;
                        }
                        
                        $manager = User::findOne($pm->manager_id);
                        $pm->date = ($item['date']) ? date("Y-m-d",strtotime($item['date'])) : $pm->date;
                    }else{
                       $pm = new PaymentsExpenses;
                       $pm->date = ($item['date']) ? date("Y-m-d",strtotime($item['date'])) :date("Y-m-d"); 
                       $pm->manager_id = (int)$item['manager_id'];
                        
                        
                       $manager = User::findOne((int)$item['manager_id']);    
                       //Определаем вид оплаты
                       if(Yii::$app->authManager->checkAccess($pm->manager_id,"client")){
                            $pm->plus = 1;
                       }else{
                            $pm->plus = 0;
                       }

                    }
                    
                    
                    
                    if(!isset($manager->id)){
                        $answer['result'] = 0;
                        $answer['messages'][] = "Менеджер не определен!";
                        return $answer;
                    }
                    //Оплата которая пришла из frontend
                    $n_sum = round(trim(strip_tags($item['sum'])),2);
                    
                    $client = $manager->client;
                    $card_percent = isset($client->id) ? $client->payment_clearing : 0;
                    
                    //определяем виды оплат
                    $toreport = (int)$item['toreport'];
                       
                    //получаем курс
                    $course = round(trim(strip_tags($item['course'])),4);
                        
                    if($toreport == 1){
                        //сумма $
                        $sum = $n_sum;
                        $sum_cash = round($course * $sum,2);
                        $comission =  $sum_cash * $card_percent/100;
                        $sum_card = $sum_cash + $comission;
                        
                    }elseif($toreport == 2){
                        //сумма руб
                        $sum_cash = $n_sum;
                        
                        $sum = round($sum_cash/$course,2);
                        $comission =  round($sum_cash * $card_percent/100,2);
			            $sum_card = round($sum_cash + $comission,2);
                        
                    }elseif($toreport == 3){
                        //сумма б/Н руб
                        $sum_card = $n_sum;
                        $sum_cash = round($sum_card/(1 + $card_percent/100),2);
                        $sum = round($sum_cash/$course,2);
                    }
                    
                    $pm->sum = $sum;
                    $pm->sum_cash = $sum_cash;
                    $pm->sum_card = $sum_card;

                    $pm->course = $course;
                    $pm->toreport = $toreport;
                    
                    $pm->comment = trim(strip_tags($item['comment']));
                    if(isset($item['organisation'])){
                        $org = Organisation::findOne((int)$item['organisation']);
                        if(isset($org->id)){
                            $pm->organisation = $org->id;
                            $pm->payment = $org->payment;
                        }
                        
                    }

                    if($pm->save(1)){
                        $answer['messages'][] = "Оплата с суммой ".$item['sum'].' добавлена';
                        
                        $answer['result'] = 1;
                    }else{
                        $answer['messages'][] = "Оплата с суммой ".$item['sum'].' не добавлена';
                    }
                } 
            }

            //Редактирование если есть расход
            if(isset($post['ExpensesManager']) && $post['ExpensesManager'] && count($post['ExpensesManager'])){
                
                foreach ($post['ExpensesManager'] as $key => $item) {
                    if(isset($item['id']) && (int)$item['id']){

                        $em = ExpensesManager::findOne((int)$item['id']);
                        if ($em->id === NULL){
                            $answer['messages'][] = "Оплата с суммой ".$item['sum'].' не добавлена';
                            continue;
                        }

                        $em->cost = round(trim(strip_tags($item['sum'])),2);
                        $em->comment = trim(strip_tags($item['comment']));


                        $manager = User::findOne($em->manager_id);

                        if(isset($item['organisation']))
                            $em->organisation = (int)$item['organisation'];

                        if($em->save(1))
                            $answer['result'] = 1;
                    }
                     
                } 
            }  
        }
        
        if($answer['result'] == 1){
            //Обновление сверки пользователя
            try {
                if(isset($manager) && isset($manager->id)){
                    $manager->refreshSverka();
                }
            } catch (Exception $e) {}
        }
        
            
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
                    
                    //Опасно удалять
                    $exp->delete();

                    //Обновление сверки пользователя
                    try {
                        User::refreshUserSverka($exp->manager_id);
                    } catch (Exception $e) {}

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


    public function actionOrgReport(){
        
        $PaymentsExpensesReport = new PaymentsExpensesReport;
        $dataProvider = $PaymentsExpensesReport->search(Yii::$app->request->queryParams);
        
        return $this->render('orgreport',['dataProvider'=>$dataProvider,'PaymentsExpensesReport'=>$PaymentsExpensesReport]);
    }




    public function actionRegisterEvent(){

        if(Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $post = Yii::$app->request->post();

            $post['user_id'] = Yii::$app->user->id;
            
            $action = UserAction::register($post);
            
            return isset($action->id) && $action->id;

        }else{
            return Yii::$app->response->redirect(['site/index']);
        }
        
    }


    public function actionRemoveEvent(){

        if(Yii::$app->request->isAjax){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $post = Yii::$app->request->post();

            $post['user_id'] = Yii::$app->user->id;
            
            $action = UserAction::close($post);
            
            return $action;

        }else{
            return Yii::$app->response->redirect(['site/index']);
        }
        
    }
    
}
