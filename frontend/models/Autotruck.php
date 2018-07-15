<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\App;
use common\models\Status;
use common\models\Client;
use common\models\User;
use frontend\models\AppTrace;
use frontend\models\AutotruckNotification;
use yii\db\Query;
use frontend\helpers\Mail;
use common\models\SupplierCountry;
use frontend\models\ExpensesManager;
use frontend\helpers\Checkexcel;


/**
*
*
*
*/

class Autotruck extends ActiveRecord
{

    public $tempFiles = null;

    const SCENARIO_CREATE = "create";

	public function rules(){
		return [
            // name, email, subject and body are required
            [['name'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => true,'checkExtensionByMimeType'=>false, 'extensions' => 'xls,xlsx,doc,docx,pdf,jpeg,jpg,png','maxFiles'=>20],
            ['creator','default','value'=>\Yii::$app->user->identity->id,'on'=>self::SCENARIO_CREATE]
        ];
	}


    public function scenarios(){
        return array_merge(parent::scenarios(),['SCENARIO_CREATE'=>[]]);
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
            'countryCode'=>'Страна',
            'common_weight'=>'Вес',
            'file'=>'Файл',
            'auto_number'=>'Номер машины',
            'auto_name'=>'Транспорт',
            'gtd'=>'ГТД',
            'decor'=>'Оформление',
            'creator'=>'Автор'
    		);
    }

    public function uploadFile()
    {
        if ($this->validate()) {

            $files = explode('|', $this->tempFiles);
            
            foreach ($this->file as $key => $file) {
                $basename = $file->basename;
                $fName = $basename . '_'.time().'.' . $file->extension;
                $file->saveAs('uploads/' . $fName);
                $files[] = $fName;
            }
            
            return implode('|', $files);
        } else {
            return false;
        }

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

    public function getCreatorUser(){
        return $this->hasOne(User::className(),["id"=>'creator']);
    }

    public function getActiveStatusTrace(){
        $status = $this->getActiveStatus();

        return AppTrace::find()->where(['autotruck_id'=>$this->id,'status_id'=>$this->activeStatus->id])->one();
    }

    public static function searchByKey($keyword){
        $query = new Query();
        
        $user = \Yii::$app->user->identity;
        $u_countries = \yii\helpers\ArrayHelper::map($user->accessCountry,'country_id','country_id');

        $where = "`description` LIKE '%$keyword%' OR `name` LIKE '%$keyword%'";
        $query->select("`id`,`name`,`number`,`date`,`description`,`status`")->from(self::tableName())->where($where)->andWhere(['in','country',$u_countries])->limit(5);
        
        return $query->all();
    }








    public function checkNotificationClent($client,$app){
        if(!$client) return false;
        $where = "`autotruck_id`=".$this->id." AND `status_id`=".$this->activeStatus->id." AND client_id=".$client." AND app_id=".$app;

        $notification = AutotruckNotification::find()->where($where)->one();
        return ($notification->nid)?false:true;
    }





    public function refreshClientsSverka(){

        $SQL = "SELECT c.`user_id`  
                FROM app as a
                INNER JOIN client as c ON a.client = c.id
                WHERE a.autotruck_id = {$this->id}";
        $users = \Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($users as $u_id) {
            User::refreshUserSverka($u_id);
        }

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
                 
                 $mail = count($client_model->emails) ? $client_model->emails : $client_model->user->email;
                 

                 $apps = $client['apps'];
                 $from = $client_model->managerUser->email ? $client_model->managerUser->email: "info@tedrans.com";

                if(count($apps)){

                    $data= array(
                        'apps' => $apps,
                        'activeStatus'=>$activeStatus,
                        "activeTrace"=>$activeTrace,
                        "autotruck_model"=>$autotruck_model
                    );


                        if(is_array($mail) && count($mail)){
                            foreach ($mail as $key => $e) {
                               $e = trim(strip_tags($e));
                               if($e){
                               $message = Yii::$app->mailer->compose('layouts/notification',$data)
                                ->setFrom($from)
                                ->setTo($e)
                                ->setSubject("Статус груза");
                                //Преверяем нужно ли отправлять чек для активного статуса
                                if($activeStatus->send_check){
                                    $checkexcel = new Checkexcel();
                                    $checkApps =  $client_model->getAutotruckApps($this->id);
                                    $file = $checkexcel->generateCheck($checkApps,$client_model);

                                    if(file_exists($file)){
                                        $message->attach($file);
                                    }

                                    
                                }

                                $message->send();
                                   
                               }
                            }

                        }elseif($mail != ""){
                            $message = Yii::$app->mailer->compose('layouts/notification',$data)
                            ->setFrom($from)
                            ->setTo($mail)
                            ->setSubject("Статус груза");

                            //Преверяем нужно ли отправлять чек для активного статуса
                            if($activeStatus->send_check){
                                $checkexcel = new Checkexcel();
                                $checkApps =  $client_model->getAutotruckApps($this->id);
                                $file = $checkexcel->generateCheck($checkApps,$client_model);

                                if(file_exists($file)){
                                    $message->attach($file);
                                }
                            }

                            $message->send();
                        }else{
                            continue;
                        }
                        

                        foreach ($apps as $k => $app) {
                            if(!$app->id) continue;
                            
                            $not = new AutotruckNotification();
                            $not->autotruck_id = $this->id;
                            $not->status_id = $this->activeStatus->id;
                            $not->client_id = $client_model->id;
                            $not->app_id = $app->id;
                            $not->save();
                        }
                }
            }
        }
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








    public static function getReport(){
        $sql = "SELECT  atr.id, atr.name, atr.date,atr.country, atr.course,
            SUM(case when ap.type = '0' then ap.weight else 0 end) as weight,
            SUM(case when ap.type = '0' then ap.weight*ap.rate else ap.rate end) as sum_us,
            SUM(case when ap.type = '0' then atr.course*ap.weight*ap.rate else atr.course*ap.rate end) as sum_ru,
            atr.expenses
        FROM `app` ap
            RIGHT JOIN (
                SELECT a.id, a.name, a.date,c.country, a.course,SUM(exp.cost) as expenses
                FROM `autotruck` as a 
                LEFT JOIN `expenses_manager` exp ON exp.autotruck_id = a.id 
                LEFT JOIN supplier_countries c ON c.id = a.country 
                GROUP BY a.`id`
                ) atr ON ap.autotruck_id = atr.`id` 
        GROUP BY atr.`id` ORDER BY ap.id DESC";

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        return $command->queryAll();
    }













    public function unlinkFile($file = null){
       
        if(!$file || !file_exists('uploads/'.$file)) return null;
    

        if($this->file != ''){

            $files = explode('|', $this->file);
            if(count($files)){
                $new_files = array();
                foreach($files as $key => $item) {
                    if($file == $item){
                        if(file_exists('uploads/'.$file)){
                            unlink('uploads/'.$file);
                        }
                    }else{
                        $new_files[] = $item;
                    }
                }

                $this->file = implode('|', $new_files);
                return $this->save();
                
            }
        }
    }


    public function fileExists($file){
        if(!$file || !$this->file) return null;

        $files = explode("|", $this->file);
        if(count($files)){
            return in_array($file,$files);
        }
        return 0;
    }


    public function getCommon_weight(){

        $sql = "SELECT SUM(a.weight) as common_weight  FROM autotruck at
                INNER JOIN app a ON a.autotruck_id = at.id
                WHERE at.id = ".$this->id." AND a.type = '0'";

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        $res = $command->queryOne();
        
        
        return  sprintf("%.2f", $res['common_weight']);
    }




    public function setAllAppOutStock($value){

        if($this->id){
            $sql = "UPDATE app SET `out_stock`={$value} WHERE `autotruck_id` = {$this->id}";

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);

            return $command->execute();

        }

        return 0;
    }

    public function getCountOutStockApp(){

        if($this->id){
            $sql = "SELECT COUNT(1) as count FROM app  WHERE `out_stock`=1 AND `autotruck_id` = {$this->id}";

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $res = $command->queryOne();
            return $res['count'];

        }

        return 0;
    }


    public function getAppCountPlace($client = null){
        
        $conditionClient = $client > 0 ? " AND `client`={$client}" : ""; 
        
        $sql = "SELECT SUM(count_place) FROM ".App::tableName()." WHERE autotruck_id = ".$this->id." ".$conditionClient;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        return $command->queryScalar();
    }

    public function getAppCountPackage($package,$client = null){
        
        $conditionClient = $client > 0 ? " AND `client`={$client}" : ""; 
        
        $sql = "SELECT count(id) FROM ".App::tableName()." WHERE autotruck_id = ".$this->id." and package = ".$package." ".$conditionClient;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        return $command->queryScalar();
    }


    public function getAppCountPlacePackage($package,$client = null){
        
        $conditionClient = $client > 0 ? " AND `client`={$client}" : "";
        
        $sql = "SELECT SUM(count_place) FROM ".App::tableName()." WHERE autotruck_id = ".$this->id." and package = ".$package." ".$conditionClient;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        return $command->queryScalar();
    }


    
}