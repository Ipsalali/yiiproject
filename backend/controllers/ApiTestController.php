<?php
namespace backend\controllers;

use Yii;
use yii\base\Controller;
use yii\filters\VerbFilter;
use api\soap\models\Brigade;
use api\soap\models\Responce;
use api\soap\Exceptions\ApiException;
use api\soap\Api;

use api\soap\test\requests\Test;

/**
 * ApiTest controller
 */
class ApiTestController extends Controller
{   


    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            
        ];
    }
    

    
    
    public function actionIndex(){
        echo "\n---Start testing";
        $this->actionTest();
        echo "<br>";
        $this->actionUnloadbrigade();
        echo "<br>";
        $this->actionUnloadtechnic();echo "<br>";
        $this->actionUnloadline();echo "<br>";
        $this->actionUnloadnomenclature();echo "<br>";
        $this->actionUnloadtypeofwork();echo "<br>";
        $this->actionUnloadboundary();echo "<br>";
        $this->actionUnloadobject();echo "<br>";
        $this->actionUnloadproject();echo "<br>";
        $this->actionUnloadworker();echo "<br>";
        $this->actionUnloadremnant();echo "<br>";
        $this->actionUnloadraport();echo "<br>";

        echo "\n---Finish testing<br>";
    }

    
    public function actionTest($msg = null){
        echo "<br>---Method Test<br>";

        $msg = "console tester";
        
        $answer = Yii::$app->testclient->getClient()->test("test message"); 

        print_r($answer);
    }


  
    public function actionUnloadbrigade($brigades = null){

        $par = [
            ['guid'=>'1asdasjdhuu32423jkasdfa','name'=>'Бригада100'],
            ['guid'=>'1asdasjdhuu32423jkasdfa','name'=>'Бригада100']
        ];

        $req = new \stdClass();
        
        $req->brigades = $par;
    
        $par['brigades']=$par;

        $client = Yii::$app->testclient->getClient();
        
        $answer = $client->unloadbrigade($par);
        
        print_r($answer);
    }




    
    public function actionUnloadworker($workers = null){


        $par = [
            [
                'guid'=>'10sdasjdhuu32423jkasdfa',
                'name'=>'Бригада1',
                'ktu'=>1.2,
                'is_master'=>true
            ]
        ];

        $answer = Yii::$app->testclient->getClient()->unloadworker(['workers'=>$par]); 

        print_r($answer);

    }






    
    public function actionUnloadtechnic($technics = null){

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Газель07',
                'marka'=>"Газель",
                'number'=>"soccer"
            ]
        ];

        $req = new \stdClass();
        
        $req->technics = $par;
        $answer = Yii::$app->testclient->getClient()->unloadtechnic(['technics'=>$par]); 

        print_r($answer);
    }






    public function actionUnloadobject($objects = null){

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Газель07',
                'boundary_guid'=>"1asdasjdhuu32413jkasdfa"
            ]
        ];


        $answer = Yii::$app->testclient->getClient()->unloadobject(['objects'=>$par]); 

        print_r($answer);
    }





    
    public function actionUnloadboundary($boundaries = null){
        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Граница 1'
            ]
        ];


        $answer = Yii::$app->testclient->getClient()->unloadboundary(['boundaries'=>$par]); 

        print_r($answer);
    }



    
    public function actionUnloadproject($projects = null){

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Проект 1',
                'objects_guids'=>[
                    '1asdasjdhuu32413jkasdfa',
                ]
            ]
        ];


        $answer = Yii::$app->testclient->getClient()->unloadproject(['projects'=>$par]); 

        print_r($answer);
    }
    



    public function actionUnloadtypeofwork($works = null){

        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Работа2',
            ]
        ];

        $answer = Yii::$app->testclient->getClient()->unloadtypeofwork(['works'=>$par]); 

        print_r($answer);
    }

    



    public function actionUnloadline($lines = null){
        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'Линия 1.1.1',
            ],
        ];

        $answer = Yii::$app->testclient->getClient()->unloadline(['lines'=>$par]); 

        print_r($answer);
    }






    public function actionUnloadnomenclature($nomenclatures = null){
        $par = [
            [
                'guid'=>'1asdasjdhuu32413jkasdfa',
                'name'=>'абота2',
            ]
        ];

        $answer = Yii::$app->testclient->getClient()->unloadnomenclature(['nomenclatures'=>$par]); 

        print_r($answer);
    }



    public function actionUnloadremnant(){
        $par = [
            [
                'brigade_guid'=>'1asdasjdhuu32423jkasdfa',
                'nomenclature_guid'=>'1asdasjdhuu32413jkasdfa',
                'count'=>10
            ]
        ];

        

        $answer = Yii::$app->testclient->getClient()->unloadremnant(['remnants'=>$par]); 

        print_r($answer);
    }



    public function actionUnloadraport(){
        $par = [
            [
                'guid'=>'2asdasjdhuu32423jkasdfa',
                'number'=>'1asdasjdhuu32413jkasdfa',
                'status'=>"ывфывлжд",
                'created_at'=>date("Y-m-d\TH:i:s"),
                'starttime'=>date("H:i:s",time()-3600),
                'endtime'=>date("H:i:s"),
                'temperature_start'=>10.1,
                'temperature_end'=>10.3,
                'surface_temperature_start'=>10.2,
                'surface_temperature_end'=>10.5,
                'airhumidity_start'=>10.1,
                'airhumidity_end'=>2.2,
                'brigade_guid'=>"1asdasjdhuu32423jkasdfa",
                'object_guid'=>"1asdasjdhuu32413jkasdfa",
                'boundary_guid'=>"1asdasjdhuu32413jkasdfa",
                'project_guid'=>"1asdasjdhuu32413jkasdfa",
                'master_guid'=>"10sdasjdhuu32423jkasdfa",
                'comment'=>"Тест",
                'materials'=>[
                    [
                        'nomenclature_guid'=>'1asdasjdhuu32413jkasdfa',
                        'was'=>11,
                        'spent'=>2,
                        'rest'=>8
                    ]
                ],
                'works'=>[
                    [
                        'work_guid'=>'1asdasjdhuu32413jkasdfa',
                        'line_guid'=>'1asdasjdhuu32413jkasdfa',
                        'mechanized'=>true,
                        'length'=>10,
                        'count'=>2.3,
                        'squaremeter'=>222.2
                    ]
                ],
                'consist'=>[
                    [
                        'technic_guid'=>'1asdasjdhuu32413jkasdfa',
                        'user_guid'=>'10sdasjdhuu32423jkasdfa',
                    ]
                ]
            ]
        ];

        
        $answer = Yii::$app->testclient->getClient()->unloadraport(['raports'=>$par]); 

        print_r($answer);
    }
}
