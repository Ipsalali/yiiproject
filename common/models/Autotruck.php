<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

use common\models\Status;
use common\models\Client;
use common\models\Sender;
use common\models\User;
use common\models\SupplierCountry;
use common\base\ActiveRecordVersionable;
use common\models\App;
use common\models\AppTrace;
use common\dictionaries\AutotruckState;

use frontend\helpers\Mail;
use frontend\models\ExpensesManager;
use frontend\helpers\Checkexcel;
use frontend\models\AutotruckNotification;


/**
*
*
*
*/

class Autotruck extends ActiveRecordVersionable
{

    public $tempFiles = null;
    
    /**
    * Array 
    */
    public $apps;


    /**
    * Array 
    */
    protected $statusObject;

    /**
    * Array 
    */
    public $appsCollection;

    /**
    * Array 
    */
    public $packagesCountPlace;

    /**
    * int 
    */
    public $totalCountPlace;

    const SCENARIO_CREATE = "create";

	public function rules(){
		return [
            // name, email, subject and body are required
            [['name','country'], 'required','message'=>'Обязательное поле'],
            [['name','invoice','decor','gtd','auto_name','auto_number','description'],'filter','filter'=>function($v){
                    return trim(strip_tags($v));
            }],
            ['course','number'],
            ['course','filter','filter'=>function($v){
                return $v ? round($v,4) : 0;
            }],
            ['date','filter','filter'=>function($v){
                return $v ? date('Y-m-d',strtotime($v)) : date("Y-m-d");
            }],
            [['status','country'],'integer'],
            [['status','country','import_source','guid'],'default','value'=>null],
            ['imported','default','value'=>0],
            [['file'], 'file', 'skipOnEmpty' => true,'checkExtensionByMimeType'=>false, 'extensions' => 'xls,xlsx,doc,docx,pdf,jpeg,jpg,png','maxFiles'=>20],
            ['creator','default','value'=>\Yii::$app->user->identity->id,'on'=>self::SCENARIO_CREATE],
            ['state', 'default', 'value' => AutotruckState::CREATED],
            ['state', 'in', 'range' => [
                    AutotruckState::CREATED, 
                    AutotruckState::TO_EXPORT,
                    AutotruckState::EXPORTED
                ]
            ],
        ];
	}



    public static function versionableAttributes(){
        return [
            'name',
            'invoice',
            'number',
            'date',
            'description',
            'status',
            'state',
            'course',
            'country',
            'file',
            'auto_number',
            'auto_name',
            'gtd',
            'decor',
            'creator',
            'isDeleted'
        ];
    }


    public function load($data, $formName = null){
        $this->tempFiles = $this->file;
        if(parent::load($data, $formName)){

            if(is_array($this->file)){
                $this->file = $this->tempFiles;
            }
            return true;
        }

        return false;
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
    		'name'=>'Название',
            'invoice'=>'Инвойс',
    		'number'=>'Номер',
    		'date'=>'Дата',
    		'description'=>'Комментарии',
            'status'=>'Статус',
            'statusTitle'=>'Статус',
            'state'=>'Состояние',
            'stateTitle'=>'Состояние',
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
        if(!$this->id) return array();

        return App::find()->where('autotruck_id='.$this->id)->andWhere(['isDeleted'=>0])->all();
    }


    public function getAppsArray(){
        if(!$this->id) return array();

        return (new Query)->
                            select(['a.*','cl.name as client_name','s.name as sender_name'])->
                            from(['a'=>App::tableName()])->
                            leftJoin(['cl'=>Client::tableName()],' cl.id = a.client')->
                            leftJoin(['s'=>Sender::tableName()],' s.id = a.sender')->
                            where('autotruck_id='.$this->id)->
                            andWhere(['a.isDeleted'=>0])->
                            all();
    }


    public function setApps($apps){
        if(is_array($apps)){
            return $this->apps = $apps;
        }
    }




    /**
    *
    * return array - модели с ошибками
    * return false - transfers are empty
    * return true - все ок
    * return 2 - не все услуги были добавлены status warning
    */
    public function saveApps($apps){
        if(!is_array($apps) || !count($apps)) return false;

        //производим проверку на валидность
        $errors = [];
        $models = [];
        foreach ($apps as $key => $t) {
            if(isset($t['id']) && (int)$t['id']){
                $model = App::findOne((int)$t['id']);

                if(!isset($model->id) || !$model->id){
                    $model = new App();
                }
            }else{
                $model = new App();
            }

            $t['autotruck_id'] = $this->id;
            $data = ['App'=>$t];
            if(!$model->load($data) || !$model->validate()){
                array_push($errors, $model);
            }
            
            array_push($models, $model);
        }

        if(!count($errors)){

            $answer = true; 
            foreach ($models as $key => $m) {
                if(!$m->save(1)){
                    $answer = 2;
                }
            }

            return $answer;

        }else{
            return $models;
        }
    }



    /**
    *
    * return array - модели с ошибками
    * return false - expenses are empty
    * return true - все ок
    * return 2 - не все услуги были добавлены status warning
    */
    public function saveExpenses($expenses){
        if(!is_array($expenses) || !count($expenses)) return false;

        //производим проверку на валидность
        $errors = [];
        $models = [];
        foreach ($expenses as $key => $t) {
            if(isset($t['id']) && (int)$t['id']){
                $model = ExpensesManager::findOne((int)$t['id']);

                if(!isset($model->id) || !$model->id){
                    $model = new ExpensesManager();
                }
            }else{
                $model = new ExpensesManager();
            }

            $t['autotruck_id'] = $this->id;
            $t['date'] = isset($t['date']) && $t['date'] ? $t['date'] : $this->date;
            $data = ['ExpensesManager'=>$t];
            if(!$model->load($data) || !$model->validate()){
                array_push($errors, $model);
            }
            
            array_push($models, $model);
        }

        if(!count($errors)){

            $answer = true; 
            foreach ($models as $key => $m) {
                if(!$m->save(1)){
                    $answer = 2;
                }else{
                    //обновление сверки
                    try {
                        User::refreshUserSverka($m->manager_id);
                    } catch (Exception $e) {}
                }
            }

            return $answer;

        }else{
            return $models;
        }
    }





    public function getExpensesManager(){
        if(!$this->id) return array();
        return ExpensesManager::getAutotruckExpenses($this->id);
    }





    public function getTraceStory(){
        if(!$this->id) return array();

        return AppTrace::find()->where('autotruck_id='.$this->id)->orderBy([
        'trace_date' => SORT_DESC
      ])->all();
    }





    public function getActiveStatus(){

        if($this->status && !$this->statusObject || !($this->statusObject instanceof Status)){
            $this->statusObject = Status::findOne(['id' => $this->status]);
        }
        
        return $this->statusObject;
    }



    public function getStatusTitle(){
        $status = $this->activeStatus;
        return isset($status->id) ? $status->title : "";
    }



    public function getNeedToSendCheck(){
        return $this->status && $this->activeStatus->send_check;
    }



    public function getCreatorUser(){
        return $this->creator ? User::findOne(["id"=>$this->creator]) : null;
    }






    /**
    * @return AppTrace;
    */
    public function getActiveStatusTrace(){
        if(!$this->id || !$this->status) return array();

        return AppTrace::find()->where(['autotruck_id'=>$this->id,'status_id'=>$this->activeStatus->id])->one();
    }






    public static function searchByKey($keyword){
        $query = new Query();
        
        $user = Yii::$app->user->identity;
        $u_countries = \yii\helpers\ArrayHelper::map($user->accessCountry,'country_id','country_id');

        $where = "`description` LIKE '%$keyword%' OR `name` LIKE '%$keyword%'";
        $query->select("`id`,`name`,`number`,`date`,`description`,`status`")->from(self::tableName())->where($where)->andWhere(['in','country',$u_countries])->limit(5);
        
        return $query->all();
    }





    public function refreshClientsSverka(){

        $sql = "SELECT DISTINCT c.`user_id`  
                FROM app as a
                INNER JOIN client as c ON a.client = c.id
                WHERE a.autotruck_id = {$this->id}";
        $users = Yii::$app->db->createCommand($sql)->queryAll();

        foreach ($users as $u_id) {
            if(isset($u_id['user_id'])){
                User::refreshUserSverka($u_id['user_id']);   
            }
        }

    }
    




    public function checkNotificationClient($client,$app){
        if(!$client) return false;
        if(!$this->status) return false;
        
        $where = "`autotruck_id`=".$this->id." AND `status_id`=".$this->status." AND client_id=".$client." AND app_id=".$app;

        $notification = AutotruckNotification::find()->where($where)->one();
        
        return (isset($notification->nid) && $notification->nid) ? false : true;
    }







    public function sendNotification(){
        
        if(!$this->status) return false;
        
        $apps = $this->getApps();
        $client_apps = array();
        
        if(count($apps)){
            foreach ($apps as $key => $app) {
                if($app->client){
                    //Проверяем нужно ли отправить уведомление текущему клиенту.
                    if(!$this->checkNotificationClient($app->client,$app->id)) continue;
                    
                    $client_apps[$app->client]['client'] = $app->client;
                    $client_apps[$app->client]['apps'][$app->id] = $app;   
                }
            }
        }
        
        
        $activeStatus = $this->activeStatus;
        $activeTrace =  $this->activeStatusTrace;
        $autotruck_model = $this;
        if(count($client_apps)  && isset($activeStatus->id)){
            
            foreach ($client_apps as $key => $client) {

                $client_model = Client::findOne($client['client']);
                 
                $mail = count($client_model->emails) ? $client_model->emails : $client_model->user->email;
                 
                $apps = $client['apps'];
                $managerUser = $client_model->managerUser;
                $from = (isset($managerUser->email)) && $managerUser->email ? $managerUser->email: "info@tedrans.com";
                

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

                                if(!defined('YII_ENV_TEST') || !YII_ENV_TEST){
                                    $message->send();
                                }
                                
                                   
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

                            if(!defined('YII_ENV_TEST') || !YII_ENV_TEST){
                                $message->send();
                            }
                        }else{
                            continue;
                        }
                        

                        foreach ($apps as $k => $app) {
                            if(!$app->id) continue;
                            
                            $not = new AutotruckNotification();
                            $not->autotruck_id = $this->id;
                            $not->status_id = $this->status;
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
                WHERE a.`isDeleted`=0
                GROUP BY a.`id`
                ) atr ON ap.autotruck_id = atr.`id`
        WHERE ap.`isDeleted`=0
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
                return $this->save(1);
                
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
            $sql = "SELECT COUNT(1) as count FROM app  WHERE `out_stock`=1 AND `autotruck_id` = {$this->id} AND `isDeleted`=0";

            $connection = Yii::$app->getDb();
            $command = $connection->createCommand($sql);
            $res = $command->queryOne();
            return $res['count'];

        }

        return 0;
    }


    public function getAppCountPlace($client = null){
        if(!$this->id) return null;
        $conditionClient = $client > 0 ? " AND `client`={$client}" : ""; 
        
        $sql = "SELECT SUM(count_place) FROM ".App::tableName()." WHERE autotruck_id = ".$this->id." AND `isDeleted`=0 ".$conditionClient;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        return $command->queryScalar();
    }

    public function getAppCountPackage($package,$client = null){
        if(!$this->id) return null;
        $conditionClient = $client > 0 ? " AND `client`={$client}" : ""; 
        
        $sql = "SELECT count(id) FROM ".App::tableName()." WHERE autotruck_id = ".$this->id." AND `isDeleted`=0 AND package = ".$package." ".$conditionClient;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        return $command->queryScalar();
    }


    public function getAppCountPlacePackage($package,$client = null){
        
        if(!$this->id) return null;

        $conditionClient = $client > 0 ? " AND `client`={$client}" : "";
        
        $sql = "SELECT SUM(count_place) FROM ".App::tableName()." WHERE autotruck_id = ".$this->id." AND `isDeleted`=0 AND package = ".$package." ".$conditionClient;
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        return $command->queryScalar();
    }




    public function getHistory(){
        if(!$this->id) return false;

        return (new Query)
                ->select([
                    'rs.*',
                    'u.name as creator_name',
                    'u.username as creator_username',
                    'st.title as status_title',
                    'sc.country as country_title'
                ])
                ->from(['rs'=>self::resourceTableName()])
                ->leftJoin(['u'=>User::tableName()]," rs.creator_id = u.id")
                ->leftJoin(['st'=>Status::tableName()]," st.id = rs.status")
                ->leftJoin(['sc'=>SupplierCountry::tableName()]," sc.id = rs.country")
                ->where([static::resourceKey()=>$this->id])
                ->orderBy(["rs.id"=>SORT_DESC])
                ->all();
    }


    
}