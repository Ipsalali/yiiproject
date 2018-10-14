<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

use backend\modules\UserSearch;
use backend\models\{Autotruck};

use common\models\User;
use common\models\AutotruckImport;

class ImportController extends Controller
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
                        'actions' => ['index','save-autotruck'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'save-autotruck' => ['post'],
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




    public function actionIndex($id = null)
    {   
        if($id){
            $autotruckImport =  AutotruckImport::findOne($id);
            $autotruckImport = isset($autotruckImport->id) ? $autotruckImport : new AutotruckImport();
        }else{
            $autotruckImport = new AutotruckImport();
        }
        

        $post = Yii::$app->request->post();
        if(isset($post['AutotruckImport'])){

            if(isset($_FILES['AutotruckImport']['name']['file']) && $_FILES['AutotruckImport']['name']['file']){
                $file = UploadedFile::getInstance($autotruckImport, 'file');
                
                if($file && file_exists($file->tempName)){
                    $fName = $file->name;
                    
                    $autotruckImport->name = $fName;
                    
                    $fParts = explode(".", $fName);
                    $fParts = $fParts ? $fParts : [];
                    $autotruckImport->extension = end($fParts);

                    $autotruckImport->fileBinary = file_get_contents($file->tempName);
                
                }
            }

            if($autotruckImport->load($post) && $autotruckImport->save()){
                Yii::$app->session->setFlash("success",'Файл загружен');
                return $this->redirect(['import/index','id'=>$autotruckImport->id]);
            }else{
                foreach ($autotruckImport->getErrors() as $attribute => $errors) {
                    $msg = is_array($errors) ? implode("\n", $errors) : $errors;
                    Yii::$app->session->setFlash("danger", $msg);
                }
            }
        }

        return $this->render('index',['autotruckImport'=>$autotruckImport]);
    }




    public function actionSaveAutotruck(){

        $post = Yii::$app->request->post();
        
        
        if(isset($post['Autotruck']) && isset($post['Autotruck']['import_source']) && (int)$post['Autotruck']['import_source']){
            
            $autotruck = null;
            
            if(isset($post['Autotruck']['id']) && (int)$post['Autotruck']['id']){
                $autotruck = Autotruck::findOne((int)$post['Autotruck']['id']);
            }
            
            if(!isset($autotruck->id)){
                $autotruck = Autotruck::findOne(['import_source'=>(int)$post['Autotruck']['import_source'],'name'=>trim(strip_tags($post['Autotruck']['name']))]);
            }
            
            
            if(!isset($autotruck->id)){
                $autotruck = new Autotruck();
                $autotruck->scenario = Autotruck::SCENARIO_CREATE;
            }
            

            if($autotruck->load($post) && $autotruck->save()){
                Yii::$app->session->setFlash("success",'Заявка сохранена');
                //Добавление наименования
                if(isset($post['App']) && count($post['App'])){
                    $res = $autotruck->saveApps($post['App']);
                    if($res === true){
                        Yii::$app->session->setFlash("success",'Наименования сохранены');
                        $autotruck->imported = 1;
                        $autotruck->save();
                    }elseif(is_array($res) && count($res)){
                        Yii::$app->session->setFlash("danger",'Наименования не сохранены, не правильный формат данных!');
                        return $this->render('importErrors',['errorApps'=>$res,'autotruck'=>$autotruck]);
                    }elseif($res === 2){
                        Yii::$app->session->setFlash("warning",'Не удалось добавить наименования, при добавлении некоторых наименований и услуг, произошла ошибка!');
                    }elseif($res === false){
                        Yii::$app->session->setFlash("warning",'Услуги не найдены!');
                    }
                }
            }else{
                Yii::$app->session->setFlash("warning",'Заявка не сохранена');
            }
            
            
            return $this->redirect(['import/index','id'=>(int)$post['Autotruck']['import_source']]); 
            
        }else{
            
            return $this->redirect(['import/index']);
        }

       
    }




}
