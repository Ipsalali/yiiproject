<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use common\models\Client;

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
            [['name','client_id','sum','sum_ru','package_id'],'required'],
            [['comment','name'],'filter','filter'=>function($v){ return strip_tags(trim($v));}],
            [['sum','sum_ru'],'double'],
            ['package_id','integer'],
            ['comment','default','value'=>""],
            ['isDeleted','default','value'=>0]
        ];
	}

    public static function versionableAttributes(){
        return [
            'package_id',
            'client_id',
            'name',
            'sum',
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
            'sum'=>"Сумма",
            'sum_ru'=>"Сумма (руб)",
            'comment'=>'Комментарий'
    	);
    }

    public function getClient(){
        return $this->hasOne(Client::className(),["id"=>'client_id']);
    }

}