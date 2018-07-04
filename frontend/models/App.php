<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\Autotruck;
use common\models\Status;
use common\models\Client;
use common\models\Sender;
use common\models\TypePackaging;
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
            [['info'], 'required'],
            [['client','sender','package','count_place','type','autotruck_id','out_stock','comment','status'],'safe']
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
            'sender'=>'Отправитель',
            'count_place'=>'Количество мест',
            'package'=>'Упаковка',
            'info'=>'Информация',
    		'weight'=>'Вес',
    		'rate'=>'Ставка',
    		'summa_us'=>'Сумма',
            'status'=>'Статус',
            'comment'=>'Комментарий',
            'autotruck_id'=>'Заявка',
            'type' => 'Тип',
            'out_stock' => 'Остаток на складе'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(Autotruck::className(),["id"=>'autotruck_id']);
    }

    public function getBuyer(){
        return $this->hasOne(Client::className(),["id"=>'client']);
    }

    public function getSenderObject(){
        return $this->hasOne(Sender::className(),["id"=>'sender']);
    }


    public function getTypePackaging(){
        return $this->hasOne(TypePackaging::className(),["id"=>'package']);
    }

    
    public function afterDelete(){
        parent::afterDelete();
    }

    public function getDescription(){
        $desc = "";
        $desc .= $this->sender ? $this->senderObject->name." " : "";
        $desc .= $this->count_place ? $this->count_place." " : "";
        $desc .= $this->package ? $this->typePackaging->title." " : "";
        $desc .= $this->info;
        
        return $desc;
    }


    public static function searchByKey($keyword){
        $query = new Query();
        
        $user = \Yii::$app->user->identity;
        $u_countries = \yii\helpers\ArrayHelper::map($user->accessCountry,'country_id','country_id');

        $where = "`info` LIKE '%$keyword%' OR `comment` LIKE '%$keyword%'";
        $query->select(['app.id','app.client','app.weight','app.rate','app.comment','app.info','app.autotruck_id'])
            ->from(self::tableName())
            ->innerJoin(Autotruck::tableName(),"autotruck.id = app.autotruck_id")
            ->where($where)
            ->andWhere(['in','country',$u_countries])
            ->limit(5);;
        
        return $query->all();
    }

    

}