<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\App;
use common\models\Status;
use common\models\Client;
use frontend\models\AppTrace;
use frontend\models\AutotruckNotification;
use yii\db\Query;
use frontend\helpers\Mail;
use common\models\SupplierCountry;
use frontend\models\ExpensesManager;

/**
*
*
*
*/

class Autotruck extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['name','status'], 'required']
        ];
	}


	/**
     * Returns the static model of the specified AR class.
     * @param string $className active record class name.
     * @return Comments the static model class
     */
	public static function model($className = __CLASS__){

		return parent::model($className);
	
	}

	/**
     * @return string the associated database table name
     */

	public static function tableName(){
		return '{{%autotruck}}';
	}

	/**
     * @return array primary key of the table
     **/     
    public static function primaryKey(){
    	return array('id');
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'name'=>'Инвойс',
    		'number'=>'Номер',
    		'date'=>'Дата',
    		'description'=>'Комментарии',
            'status'=>'Статус',
            'course'=>'Курс',
            'country'=>'Страна',
            'countryName'=>'Страна поставки',
            'ruDate'=>'Дата',
            'countryCode'=>'Страна'
    		);
    }


    public function getApps(){
        return App::find()->where('autotruck_id='.$this->id)->all();
    }

    public function getExpensesManager(){
        return ExpensesManager::find()->where('autotruck_id='.$this->id)->all();
    }

    public function getTraceStory(){
        return AppTrace::find()->where('autotruck_id='.$this->id)->orderBy([
        'trace_date' => SORT_DESC
      ])->all();
    }

    public function getActiveStatus(){
        return $this->hasOne(Status::className(),["id"=>'status']);
    }

    public function getActiveStatusTrace(){
        $status = $this->getActiveStatus();

        return AppTrace::find()->where(['autotruck_id'=>$this->id,'status_id'=>$this->activeStatus->id])->one();
    }

    public static function searchByKey($keyword){
        $query = new Query();
        
        $where = "`description` LIKE '%$keyword%' OR `name` LIKE '%$keyword%'";
        $query->select("`id`,`name`,`number`,`date`,`description`,`status`")->from(self::tableName())->where($where)->limit(5);
        
        return $query->all();
    }

    public function checkNotificationClent($client,$app){
        if(!$client) return false;
        $where = "`autotruck_id`=".$this->id." AND `status_id`=".$this->activeStatus->id." AND client_id=".$client." AND app_id=".$app;

        $notification = AutotruckNotification::find()->where($where)->one();
        return ($notification->nid)?false:true;
    }

    public function sendNotification(){


        $apps = $this->getApps();
        $client_apps = array();
        if(count($apps)){
            foreach ($apps as $key => $app) {
                if($app->client){
                    //Проверяем нужно ли отправить уведомление текущему клиенту.
                    if(!$this->checkNotificationClent($app->client,$app->id)) continue;
                    
                    $client_apps[$app->client]['client'] = $app->client;
                    $client_apps[$app->client]['apps'][$app->id] = $app;   
                }
            }
        }
        //print_r($client_apps);
        $activeStatus = $this->activeStatus;
        $activeTrace =  $this->activeStatusTrace;
        $autotruck_model = $this;
        if(count($client_apps)){
            foreach ($client_apps as $key => $client) {

                

                 $client_model = Client::findOne($client['client']);
                 $mail = $client_model->user->email;
                 $apps = $client['apps'];


                if(count($apps)){
                    ob_start();
                    include '../views/modelview/notification.php';
                    $output = ob_get_clean();
                    $letter = $output;

                    $data= array(
                        'apps' => $apps,
                        'activeStatus'=>$activeStatus,
                        "activeTrace"=>$activeTrace,
                        "autotruck_model"=>$autotruck_model
                    );
                    foreach ($apps as $k => $app) {
                        if(!$app->id) continue;
                        $this->MailSend($mail,$letter);
                        $not = new AutotruckNotification();
                        $not->autotruck_id = $this->id;
                        $not->status_id = $this->activeStatus->id;
                        $not->client_id = $client_model->id;
                        $not->app_id = $app->id;
                        $not->save();
                    }
                    
                    // Yii::$app->mailer->compose('layouts/notification',$data)
                    //     ->setFrom('magomedaliev.93@mail.ru')
                    //     ->setTo($mail)
                    //     ->setSubject("Notification Yii mailer")
                    //     ->send();
                }
            }
        }
    }

    function MailSend($tomail,$html,$files=null){
            
            $sender="Notification";
            $subject="Notification";
            $text="Notification";

            $mail = new Mail();

            $mail->protocol = 'mail';
            $mail->parameter = '';
            $mail->hostname = '';
            $mail->username = '';
            $mail->password = '';
            $mail->port = 25;
            $mail->timeout = 5;
            
            //$mail->setTo($this->config->get('config_email'));
            //$mail->setTo('web-ali@yandex.ru');
            
            $mail->setTo($tomail);
            $mail->setFrom("magomedaliev.93@mail.ru");
            $mail->setSender($sender);
            $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
            $mail->setHtml($html);
            $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            $mail->setAttachment($files);
            $mail->send();
            
            return 1;//$mail->send();
    }

    public function getSupplierCountry(){
        return $this->hasOne(SupplierCountry::className(),['id'=>'country']);
    }

    public function getCountryName(){
        return $this->supplierCountry->country;
    }

    public function getRuDate(){
        return date("d.m.Y",strtotime($this->date));
    }

    public function getCountryCode(){
        return $this->supplierCountry->code;//substr(, 0,1);
    }

    public function afterDelete(){
        AppTrace::deleteAll("autotruck_id=".$this->id);
        parent::afterDelete();
    }
}