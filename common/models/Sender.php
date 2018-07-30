<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

use common\base\ActiveRecordVersionable;
/**
*
*
*
*/

class Sender extends ActiveRecordVersionable
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['name','phone'], 'required'],
            ['email','email'],
            [['isDeleted'],'default','value'=>0],
        ];
	}


    public static function versionableAttributes(){
        return [
            'phone',
            'email',
            'name',
            'isDeleted',
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
		return '{{%sender}}';
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
    		'name'=>'Имя отправителя',
            'phone'=>"Телефон",
            'email'=>'E-mail'
    		);
    }


    public static function searchByKey($keyword){
        $query = new Query();
        
        $where = "`name` LIKE '%$keyword%'  OR `phone` LIKE '%$keyword%'  OR `email` LIKE '%$keyword%'";
        $query->select(['id','name','phone','email'])->from(self::tableName())->where($where)->limit(5);;
        
        return $query->all();
    }

}