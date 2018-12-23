<?php
namespace console\controllers;
 
use Yii;
use yii\helpers\Url;
use yii\console\Controller;
use yii\db\Query;
use common\models\Request;
use common\models\Autotruck;
use soapclient\methods\LoadCustomer;
use soapclient\methods\CreateReceipts;
use common\dictionaries\AutotruckState;
 
/**
 * Daemon controller
 */
class DaemonController extends Controller {
 
    public function actionIndex() {
        echo "Yes, cron service is running.";
        Yii::info("Yes, cron service is running.",'cron');
    }
  


    
    public function actionResend() {
      // called every two minutes
      // */2 * * * * ~/sites/www/yii2/yii test
      Yii::info("Process start",'cron');
      $time_start = microtime(true);

      $errorRequests = Request::find()->where(['result'=>0,'completed'=>0])->all();
      try {
        foreach ($errorRequests as $r) {
          Yii::info('Execute '. $r['request'].' method #'.$r['id'],'cron');
          echo "Execute ". $r['request']." method #{$r['id']}\n";
          if(class_exists($r['request'])){
            $method = new $r['request'];
            
            if($method instanceof LoadCustomer){
              $params = json_decode($r['params_in'],1);
              $method->load(['LoadCustomer'=>$params]);
              if($method->validate() && $r->send($method)){
                Yii::info('Method executed success','cron');
                echo "Method executed success \n";
              }else{
                Yii::info('Method not executed','cron');
                echo "Method not executed \n";

                Yii::info($r->params_out,'cron');
                echo $r->params_out,"\n";
              }
            }elseif($method instanceof CreateReceipts){
              if(!$r->autotruck_id){
                Yii::info('autotruck_id dont have','cron');
                echo "autotruck_id dont have \n";
                continue;
              }

              $model = Autotruck::findOne([$r->autotruck_id]);
              if(!isset($model->id)){
                Yii::info('Raport dont have','cron');
                echo "Raport dont have \n";
                continue;
              }

              $params = json_decode($r['params_in'],1);

              $method->setParameters($params);
              if($r->send($method)){
                  Yii::info('Method executed success','cron');
                  echo "Method executed success\n";

                  Yii::info($r->params_out,'cron');
                  echo $r->params_out,"\n";
                  $responce = json_decode($r->params_out,1);
                  if($r->result && isset($responce['guid']) && $responce['guid'] && isset($responce['number']) && $responce['number']){
                      $model->guid = $responce['guid'];
                      $model->doc_number = $responce['doc_number'];
                      $model->state = AutotruckState::EXPORTED;
                      $model->save(1);
                      echo "autotruck exported successfully\n";
                  }else{
                      echo "autotruck doesn`t have guid or number\n";
                  } 

              }else{
                Yii::info('Request not saved','cron');
                echo "Request not saved \n";

                Yii::info($r->params_out,'cron');
                echo $r->params_out,"\n";
              }

            }
            
          }else{
            Yii::info('Method class not founded','cron');
            echo "Method class not founded \n";
          }
          
        }
      } catch (\Exception $e) {
          Yii::info($e->getMessage(),'cron');
          echo $e->getMessage(),"\n";
      }
      
      $time_end = microtime(true);
      Yii::info("Processing for ".($time_end-$time_start)." seconds",'cron');
      echo "Processing for ".($time_end-$time_start)." seconds \n ";
    }
}