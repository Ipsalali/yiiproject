<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\User;
use common\models\Organisation;
use common\models\ClientCategory;
use frontend\models\App;
use frontend\models\Autotruck;
use yii\db\Query;
use yii\db\Command;
use common\models\PaymentState;
use common\models\ClientOrganisation;

/**
*
*
*
*/

class Client extends ActiveRecord
{

    public $ipay;

	public function rules(){
		return [
            // name, email, subject and body are required
            [['name','full_name','payment_clearing','phone'], 'required'],
            
            ['contract_number','required','on'=>'save_contact_number'],

            [['name','full_name','contract_number','payment_clearing','phone','description'], 'filter','filter'=>function($v){return trim(strip_tags($v));}],
            ['email','match','pattern'=>'|^[A-Z0-9@\-_\. ,]*$|i'],
            
            ['email','filter','filter'=>function($v){
                $mails = explode(",",$v);
                $e = [];
                if(is_array($mails) && count($mails)){
                    
                    foreach ($mails as $key => $m) {
                        $e[] = trim(strip_tags($m));
                    }

                    return implode(",", $e);
                }else{
                    return "";
                }
            }],
            [['payment_clearing'],'double'],
            [['organisation_pay_id','payment_clearing','user_id','manager','organisation_pay_id','client_category_id'],'default','value'=>0]
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
		return '{{%client}}';
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
            'full_name'=>'Полное название',
            'contract_number'=>'Договор',
            'payment_clearing'=>'Оплата по безналу (%)',
    		'description'=>'Описание',
    		'phone'=>'Телефон',
            'user_id'=>'Профиль',
            'manager'=>'Ответственный',
            'client_category_id'=>'Категория',
            'email'=>'E-mail',
            'userEmail'=>'E-mail',
            'categoryTitle'=>'Категория',
            'managerName'=>'Ответственный',
            'ipay'=>'Статус оплаты заявок',
            'organisation_pay_id'=>'Организация'
    	);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeHints(){
        return array(
            'email'=>"Если у клитента несколько адресов,разделите их запятымы."
        );
    }


    public function load($data,$nameForm = null){
        $old_contact_number = $this->contract_number;
        $res = parent::load($data,$nameForm);

        //Если нет ошибок и указана организация, то контактный номер не трогаем, оставляем старое значение
        if(!count($this->getErrors()) && $this->organisation_pay_id > 0){
        
            $this->contract_number = $old_contact_number;
            
            return true;
        
        }
        return $res;
    }


    public function getUser(){
        return $this->hasOne(User::className(),["id"=>'user_id']);
    }

    public function getEmail(){
        return $this->email;
    }

    public function getEmails(){
        $emails = explode(",", $this->email);
        $mails = [$this->user->email];
        if(count($emails) && $emails[0]){
            foreach ($emails as $key => $e) {
                $mails[] = trim(strip_tags($e));
            }
        }
        return $mails;
    }







    public function getUserEmail(){
        return $this->user->email;
    }





    public function getManagerUser(){
        return $this->hasOne(User::className(),["id"=>'manager']);
    }





    public function getManagerName(){
        return $this->managerUser->username;
    }






    public function getCategory(){
        return $this->hasOne(ClientCategory::className(),["cc_id"=>'client_category_id']);
    }





    public function getCategoryTitle(){
        return $this->category->cc_title;
    }



    public function getOrganisation(){
        return $this->hasOne(Organisation::className(),["id"=>'organisation_pay_id']);
    }



    public function getName()
    {
       return $this->name;
    }



    public function getLast_name()
    {
       return $this->last_name;
    }



    public function getPhone()
    {
       return $this->phone;
    }


    public function getApps(){

        $user = \Yii::$app->user->identity;
        $u_countries = \yii\helpers\ArrayHelper::map($user->accessCountry,'country_id','country_id');

        return App::find()->innerJoin('autotruck','autotruck.id = app.autotruck_id')->where("client=".$this->id)->andWhere(['in','autotruck.country',$u_countries])->orderBy(["autotruck.date"=>SORT_DESC])->all();
    }



    public function getOwnApps(){

        return App::find()->innerJoin('autotruck','autotruck.id = app.autotruck_id')->where("client=".$this->id)->orderBy(["autotruck.date"=>SORT_DESC])->all();
    }






    //Возвращает наименования сгруппированные по заявкам
    public function getAppsSortAutotruck($apps = null){
        
        $apps = ($apps === null) ? $this->apps : $apps;
        
        $sorted =array(); 
        if($apps){

            foreach ($apps as $key => $a) {
                $sorted[$a->autotruck_id]['apps'][] = $a;
            }
        }

        return $sorted;
    }







    public function getAutotruckApps($autotruck_id){
        return App::find()->where("client=".$this->id." AND autotruck_id=".$autotruck_id)->all();
    }








    public static function searchByKey($keyword){
        $query = new Query();
        
        $where = "client.`name` LIKE '%$keyword%' OR client.`full_name` LIKE '%$keyword%' OR client.`description` LIKE '%$keyword%' OR client.`phone` LIKE '%$keyword%' OR client.`email` LIKE '%$keyword%' OR us.`email` LIKE '%$keyword%'";
        $query->select(['client.`id`','client.`name`','client.full_name','client.description','client.phone','user_id','us.email as us_email','client.email as cl_email'])
        ->from(self::tableName())
        ->leftJoin(['us'=>User::tableName()]," client.user_id = us.id")
        ->where($where)->limit(5);
        
        return $query->all();
    }







    public function findByFilter($filters){
       
        foreach ($filters as $key => $value) {
           if(!$value){continue;}
           $where[$key] = $value; 
        }
        
        if($where) return Client::find()->where($where)->all();
        return Client::find()->all();
        
    }






    //Формирует данные для графика по соотношению времени и веса заявок
    public function getDataForGrafik(){
        $sql= "SELECT at.`date`,SUM(a.`weight`) com_weight FROM app a INNER JOIN autotruck at ON (a.`autotruck_id` = at.`id`) WHERE a.`client` = " . $this->id . " GROUP by at.date ORDER BY at.date ASC ";
        //App::find()->leftJoin("autotrucks ON ")->where("client=".$this->id)->orderBy(["id"=>SORT_DESC])->all();
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $data = array();
        if($result){
            foreach ($result as $key => $value) {
                $month = date("M Y",strtotime($value['date']));
                $data[$month]['weight'] +=$value['com_weight']; 
            }
        }
        return $data;
    }






    //Статус оплаты заявок клиента
    public function getIpay(){
        $subsql1 = "(SELECT COUNT(DISTINCT a.id) FROM autotruck a 
                    INNER JOIN customer_payment cp ON a.id = cp.autotruck_id
                    INNER JOIN payment_state ps  ON ps.id = cp.payment_state_id
                    WHERE cp.client_id = ".$this->id." AND ps.end_state = 1)";

            $subsql2 = "(SELECT COUNT(DISTINCT a.id) FROM autotruck a
                INNER JOIN app ap ON ap.autotruck_id = a.id
                WHERE ap.client = ".$this->id.")";
        $sql = "SELECT DISTINCT CASE WHEN {$subsql1} = {$subsql2} THEN 1 ELSE 0 END as payed FROM client";

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $res = $command->queryOne();
        
        $this->ipay = ($res['payed'])? PaymentState::getEndState() : PaymentState::getDefaultState();

        return $this->ipay;
    }








    public function getDebt(){
        
        $sql = "select SUM(ap.summa_us) as sum
                FROM autotruck a
                INNER JOIN app ap ON ap.autotruck_id = a.id
                WHERE ap.client = {$this->id} AND 
                    (ap.autotruck_id in (
                        SELECT cp.autotruck_id 
                        FROM customer_payment cp
                        INNER JOIN payment_state ps ON ps.id = cp.payment_state_id
                        WHERE cp.client_id = {$this->id} AND cp.autotruck_id = ap.autotruck_id AND (ps.default_value = 1 OR ps.sum_state = 1))
                        OR 
                        NOT exists (SELECT cp2.autotruck_id FROM customer_payment cp2 WHERE cp2.autotruck_id  = ap.autotruck_id  AND cp2.client_id = {$this->id})
                        )";

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $res = $command->queryOne();

        return ($res['sum'])? $res['sum'] : 0;
    }





    //получаем частично оплаченную сумму
    public function getSumStateSum(){
        $sql = "select SUM(cp.sum) as sum
                FROM customer_payment cp
                LEFT JOIN payment_state ps ON ps.id = cp.payment_state_id
                WHERE cp.client_id = {$this->id} AND ps.sum_state = 1;";

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $res = $command->queryOne();

        return ($res['sum'])? $res['sum'] : 0;
    }



    public function getActualContractNumber(){
        if($this->organisation_pay_id){
            $CO = ClientOrganisation::findByClienAndOrg($this->id,$this->organisation_pay_id);
            if(isset($CO->client_id) && $CO->client_id){
                return $CO->relation_number;
            }
        }
        
        return $this->contract_number;
    }
}