<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\Autotruck;
use common\models\Status;
use common\models\Client;
use frontend\models\AppTrace;
use yii\db\Query;

/**
*
*
*
*/

class App extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['info'], 'required']
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
		return '{{%app}}';
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
    		'client'=>'Клиент',
            'info'=>'Информация',
    		'weight'=>'Вес',
    		'rate'=>'Ставка',
    		'course'=>'Курс',
            'status'=>'Статус',
            'comment'=>'Комментарий',
            'autotruck_id'=>'Заявка',
            'type' => 'Тип'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(Autotruck::className(),["id"=>'autotruck_id']);
    }

    public function getBuyer(){
        return $this->hasOne(Client::className(),["id"=>'client']);
    }

    
    public function afterDelete(){
        parent::afterDelete();
    }


    public static function searchByKey($keyword){
        $query = new Query();
        
        $where = "`info` LIKE '%$keyword%' OR `comment` LIKE '%$keyword%'";
        $query->select(['id','client','weight','rate','course','comment','info','autotruck_id'])->from(self::tableName())->where($where)->limit(5);;
        
        return $query->all();
    }

    

}