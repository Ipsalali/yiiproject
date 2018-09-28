<?php
namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
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
use common\models\Seller;
use common\models\PaymentClientByTransfer;

/**
 * Sverka controller
 */
class SverkaController extends Controller
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
                        'actions' => ['index','sverka','expenses-people-by-key'],
                        'allow' => true,
                        'roles' => ['site/sverka'],
                    ],
                    [
                        'actions'=>['addpaymentsmanager'],
                        'allow' => true,
                        'roles' => ['sverka/addpaymentsmanager'],
                    ],
                    [
                        'actions'=>['removepayajax'],
                        'allow' => true,
                        'roles' => ['sverka/removepayajax'],
                    ],
                    [
                        'actions'=>['pay-form-transfer-client','save-client-payment-transfer'],
                        'allow' => true,
                        'roles' => ['sverka/save-client-payment-transfer'],
                    ],
                    [
                        'actions'=>['remove-client-pay-by-transfer'],
                        'allow' => true,
                        'roles' => ['sverka/remove-client-pay-by-transfer'],
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



    public function actionIndex(){

        
        $params = Yii::$app->request->get();
        $data_params = array();
        $sverka = array();
        $identityIsOnlySeller = Yii::$app->user->identity->isSeller(true);
        if($identityIsOnlySeller){
            $manager = User::findOne(Yii::$app->user->id);
        }else{
            $manager = new User; 
        }
        

        if($params && isset($params['manager']) && (int)$params['manager']){
            $data_params['date_from'] = $params['date_from'];
            $data_params['date_to'] = $params['date_to'];
            
            if(!$identityIsOnlySeller && isset($params['manager'])){
                $data_params['manager'] = (int)$params['manager'];
            }else{
                $data_params['manager'] = Yii::$app->user->id;
            }
            
            $manager = User::findOne($data_params['manager']);
            
            $sverka = $manager->getPaymentsAndExpenses($data_params['date_from'],$data_params['date_to']);

            //Формируем сверку для клиента по переводам услуг
            $managerIsClient = $manager->isClient();
            if(!$identityIsOnlySeller && $managerIsClient){
                $client = $manager->client;
                if(isset($client->id)){
                    $clientSverkaByTransfer = $client->getSverkaByTransfer($data_params['date_from'],$data_params['date_to']);
                    $sellers = Seller::getSellers();
                }
            }


        }else{
            $data_params['date_from'] = date("Y-m-d",time() - (86400 * 61));
            $data_params['date_to'] = date("Y-m-d",time());
        }

        $orgs = Organisation::find()->all();
        $expensesPeople = User::getExpensesManagers();
        
        $client = isset($client) ? $client : $manager->client;
        
        $card_percent = isset($client->id) ? $client->payment_clearing : 0;
        $totalSverka = $manager->getManagerSverka(true,isset($data_params['date_to']) ? $data_params['date_to'] : null);

        if($totalSverka['sum'] > 0){
            $average_course = round($totalSverka['sum_cash']/$totalSverka['sum'],2);
        }else{
            $average_course = null;
        }
        return $this->render('sverka', [
            "orgs" => $orgs,
            "sellers"=>isset($sellers) ? $sellers : [],
            "expensesPeople"=>$expensesPeople,
            'card_percent'=>$card_percent,
            'totalSverka'=>$totalSverka,
            'average_course'=>$average_course,
            "sverka"=>$sverka,
            "manager"=>$manager,
            "client"=>$client,
            "data_params"=>$data_params,
            "clientSverkaByTransfer"=> isset($clientSverkaByTransfer) ? $clientSverkaByTransfer : null
        ]);
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
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            
            $params = Yii::$app->request->post();

            $answer = array();

            if($params['id']){
            
                $id = (int)$params['id'];

                $exp = PaymentsExpenses::findOne($id);
                if($exp){
                    $answer['result'] = (int)$params['id'];
                    
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
        
            
            
            return $answer;
        }else{
            return $this->redirect(['sverka/index']);
        }
    }





    public function actionPayFormTransferClient(){
        
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            $get = Yii::$app->request->get();
            $rowNumber = isset($get['number']) && (int)$get['number'] ? (int)$get['number'] : 0; 
            
            if(isset($get['id']) && (int)$get['id']){
                $model = PaymentClientByTransfer::findOne((int)$get['id']);

                $model = isset($model->id) ? $model : new PaymentClientByTransfer;
            }else{
                $model = new PaymentClientByTransfer;
            }

            $html = $this->renderPartial("rowPayTransferClient",['n'=>$rowNumber,'model'=>$model]);
            
            return [
                'html'=>$html
            ];
        }

        return $this->redirect(['sverka/index']);
    }




    public function actionSaveClientPaymentTransfer(){

        $post = Yii::$app->request->post();

        $result = false;
        if(isset($post['client_id']) && $post['client_id']){
            $client = Client::findOne((int)$post['client_id']);

            if(isset($client->id) && isset($post['pay'])){
                $result = $client->addTransferPayments($post['pay']);
            }
        }

        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return [
                'success'=>$result
            ];
        }

        return $this->redirect(['sverka/index']);
    }



    public function actionRemoveClientPayByTransfer(){
        if(Yii::$app->request->isAjax){
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $post = Yii::$app->request->post();
            $result = false;
            if(isset($post['id']) && (int)$post['id']){
                $model = PaymentClientByTransfer::findOne((int)$post['id']);

                if(isset($model->id)){
                    $model->delete();
                    $result = true;
                }
            }

            return [
                'success'=>$result
            ];
        }

        return $this->redirect(['sverka/index']);
    }

    
}
