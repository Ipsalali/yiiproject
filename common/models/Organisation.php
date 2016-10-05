<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;

/**
*
*
*
*/

class Organisation extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['bank_name','bank_check','bik','inn','kpp','org_name','org_check','org_address','headman'], 'required'],
            [['bank_name','org_name','org_address','headman'],'string'],
            [['bank_check','bik','inn','kpp','org_check'],'integer']
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
		return '{{%organisation}}';
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
    		'bank_name'=>'Наименование банка',
    		'bank_check'=>'Счет банка',
    		'bik'=>'БИК',
            'inn'=>'ИНН',
            'kpp'=>'КПП',
            'org_name'=>'Наименование организации',
            'org_check'=>'Счет организации',
            'org_address'=>'Адрес организации',
            'active'=>'Действующий',
            'headman'=>'Руководитель'
    	);
    }

}