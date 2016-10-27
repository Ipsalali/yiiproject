<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\User;
use common\models\ClientCategory;
use frontend\models\App;
use frontend\models\Autotruck;
use yii\db\Query;
use yii\db\Command;
use common\models\PaymentState;

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
            [['name','full_name','contract_number','payment_clearing','phone'], 'required'],
            [['payment_clearing'],'double']
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
            'categoryTitle'=>'Категория',
            'managerName'=>'Ответственный',
            'ipay'=>'Статус оплаты заявок'
    	);
    }


    public function getUser(){
        return $this->hasOne(User::className(),["id"=>'user_id']);
    }

    public function getEmail(){
        return $this->user->email;;
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
        return App::find()->where("client=".$this->id)->orderBy(["id"=>SORT_DESC])->all();
    }

    //Возвращает наименования сгруппированные по заявкам
    public function getAppsSortAutotruck(){
        $apps = $this->apps;
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
        
        $where = "`name` LIKE '%$keyword%' OR `full_name` LIKE '%$keyword%' OR `description` LIKE '%$keyword%' OR `phone` LIKE '%$keyword%'";
        $query->select(['id','name','description','phone','user_id'])->from(self::tableName())->where($where)->limit(5);;
        
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
        $sql = "select SUM(ap.rate * ap.weight) as sum
                FROM autotruck a
                INNER JOIN app ap ON ap.autotruck_id = a.id
                WHERE ap.client = {$this->id} AND 
                    (ap.autotruck_id in (
                        SELECT cp.autotruck_id 
                        FROM customer_payment cp
                        INNER JOIN payment_state ps ON ps.id = cp.payment_state_id
                        WHERE cp.client_id = {$this->id} AND cp.autotruck_id = ap.autotruck_id AND (ps.default = 1 OR ps.sum_state = 1))
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
}