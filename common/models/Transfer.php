<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use common\models\Client;

use common\models\Currency;
use common\base\ActiveRecordVersionable;
/**
*
*
*
*/

class Transfer extends ActiveRecordVersionable
{

    

	public function rules(){
		return [
            [['name','client_id','sum','sum_ru','currency','course','package_id'],'required'],
            [['comment','name','course'],'filter','filter'=>function($v){ return strip_tags(trim($v));}],
            [['course'],'number'],
            ['currency','in','range'=>[Currency::C_DOLLAR,Currency::C_EURO]],
            [['sum','sum_ru'],'double'],
            ['package_id','integer'],
            ['comment','default','value'=>""],
            ['isDeleted','default','value'=>0],
            [['course','currency'],'default','value'=>null]
        ];
	}




    public static function versionableAttributes(){
        return [
            'package_id',
            'client_id',
            'name',
            'sum',
            'currency',
            'course',
            'sum_ru',
            'comment',
            'isDeleted'
        ];
    }

	


	public static function model($className = __CLASS__){
		return parent::model($className);	
	}

	

	public static function tableName(){
		return "{{%transfer}}";
	}

	    
    public static function primaryKey(){
    	return array('id');
    }


    
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'name'=>'Наименование',
            'client_id'=>"Клиент",
            'currency'=>'Валюта',
            'course'=>'Курс',
            'sum'=>"Сумма",
            'sum_ru'=>"Сумма (руб)",
            'comment'=>'Комментарий'
    	);
    }

    public function getClient(){
        return $this->hasOne(Client::className(),["id"=>'client_id']);
    }



    public function getHistory(){
        if(!$this->id) return false;

        return (new Query)
                ->select([
                    'rs.*',
                    'u.name as creator_name',
                    'u.username as creator_username',
                    'c.full_name as client_name'
                ])
                ->from(['rs'=>self::resourceTableName()])
                ->leftJoin(['u'=>User::tableName()]," rs.creator_id = u.id")
                ->leftJoin(['c'=>Client::tableName()]," c.id = rs.client_id")
                ->where([static::resourceKey()=>$this->id])
                ->orderBy(["rs.id"=>SORT_DESC])
                ->all();
    }


    

}