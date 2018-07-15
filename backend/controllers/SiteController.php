<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use backend\models\LoginForm;
use yii\filters\VerbFilter;
use common\models\User;

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
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['addrole', 'sverka-restart','index','list','user','permission','extendperm','delete','userform'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
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
        ];
    }




    public function actionIndex()
    {   

        //$this->layout = "/sidebars/no_sidebar.php";
        return $this->render('index');
    }




    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            $this->layout = "/sidebars/no_sidebar.php";
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }




    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }





    public function actionList(){

        $user = new User;

        $data = $user->find()->all();
        
        //$this->layout = 'sidebars/sb_left';

        return $this->render('list',array('data'=>$data));

    }




    public function actionUser($id = 0){

        $user =User::findOne($id);

        $this->layout = 'sidebars/sb_left';

        return $this->render('user',array('user'=>$user));

    }




    public function actionUserform($id=null){
        $user = new User();
        if($id){
            $user = User::findIdentity($id);
        }
        else{
            $user = new User();
        }

        if(Yii::$app->request->post()){
            $post = Yii::$app->request->post();

           

            $user->username = $post['User']['username'];
            $user->email = $post['User']['email'];
            $user->name = $post['User']['name'];
            $user->phone = $post['User']['phone'];
            if(!$id){
                $user->setPassword($post['User']['password']);
                $user->generateAuthKey();
            }

            
            
            if ($user->save()) {
                if(!$id){                    
                    $userRole = Yii::$app->authManager->getRole('main_manager');
                    Yii::$app->authManager->assign($userRole, $user->getId());
                    
                }elseif(isset($post['resetPass']) && (int)$post['resetPass']){
                    $user->setPassword($post['User']['password']);
                    $user->generateAuthKey();
                    $user->save();
                }

                if(isset($post['manager_country'])){
                    $user->addAccessCountry($post['manager_country']);
                }else{
                    $user->removeAccessCountry();
                }
                
                Yii::$app->response->redirect(array("site/list"));
            }
        }

        return $this->render("userform",array("user"=>$user));
    }





    //Добавление роли
    public function actionAddrole(){


        $role = Yii::$app->authManager->createRole('manager');
        $role->description = 'manager';
        Yii::$app->authManager->add($role);
 
    }




    //Добавление разрешении
    public function actionPermission(){

        $permit = Yii::$app->authManager->createPermission('addClient');
        $permit->description = 'Право добавлять клиента';
        Yii::$app->authManager->add($permit);

    }




    //наследование роли
    public function actionExtendperm(){
        
        $role = Yii::$app->authManager->getRole("manager");
        $permit = Yii::$app->authManager->getPermission("addClient");
        Yii::$app->authManager->addChild($role, $permit);
    
    }

    

    
    public function actionDelete($id = NULL){

        if($id == NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("site/list"));
        }

        $user = User::findOne($id);

        if($user === NULL){
            Yii::$app->session->setFlash("StatusDeleteError");
            Yii::$app->response->redirect(array("site/list"));
        }


        $user->delete();

        Yii::$app->session->setFlash("StatusDeleted");
       // Yii::$app->getResponse->redirect(array("status/index"));
        return  Yii::$app->response->redirect(array("site/list"));
    }




    public function actionSverkaRestart(){
        $users = User::find()->all();

        try{
            foreach ($users as $u) {
                $u->refreshSverka();
            }

            Yii::$app->session->setFlash("success","Сверка перерасчитана!");
        }catch(\Exception $e){
            Yii::$app->session->setFlash("danger","Ошибка при выполнении перерасчета сверки!");
        }
        
        return Yii::$app->response->redirect(array("site/list"));
    }
}
