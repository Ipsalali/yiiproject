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
    

    public $daemon;

    public $gracefully;

    public function options($actionId){
      return ['daemon','gracefully'];
    }
    
    public function optionAliases()
    {
        return [
          'd' => 'daemon',
          'g' => 'gracefully'
        ];
    }

    public function actionIndex($do = "start", $opt = null) {

        global $argv;

        if($do) $argv[1] = $do;
        if($opt) $argv[2] = $opt;

        // print_r($argv);
        // exit;

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