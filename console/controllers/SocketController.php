<?php
namespace console\controllers;
 
use Yii;
use yii\helpers\Url;
use yii\console\Controller;
use yii\db\Query;
use WSUserActions\WSWorker;

/**
 * Socket controller
 */
class SocketController extends Controller {
  
    

    public function actionStart() {

        if(!Yii::$app->hasModule('websocket')){
          Yii::info("Module 'websocket' not enabled!");
          echo "Module 'websocket' not enabled!\n";
          return null;
        }

        $websocket = Yii::$app->getModule('websocket');

        if(!($websocket instanceof WSWorker)){
          Yii::info("Module 'websocket' must implement class WSUserActions\\WSWorker");
          echo "Module 'websocket' must implement class WSUserActions\\WSWorker\n";
          return null;
        }

        $websocket->build();
        $websocket->runAll();
    }
  


    
   
}