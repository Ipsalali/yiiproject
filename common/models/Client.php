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

/**
*
*
*
*/

class Client extends ActiveRecord
{


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
}