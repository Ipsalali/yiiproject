<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;


class Payment extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['title','code'], 'required'],
            ['code','unique','message'=>'Запись с таким кодом уже существует!'],
            ['code','integer','message'=>'Код должен быть числом.']
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
		return '{{%payments}}';
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
    		'title'=>'Заголовок',
    		'code'=>'Код'
    		);
    }

}