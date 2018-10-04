<?php
namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;

use backend\modules\UserSearch;
use backend\models\LoginForm;

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
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
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







}
