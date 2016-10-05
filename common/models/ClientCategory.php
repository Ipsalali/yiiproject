<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
*
*
*
*/

class ClientCategory extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['cc_title'], 'required']
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
		return '{{%client_category}}';
	}

	/**
     * @return array primary key of the table
     **/     
    public static function primaryKey(){
    	return array('cc_id');
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'cc_id'=>'Id',
    		'cc_title'=>'Заголовок',
            'cc_description'=>'Описание',
            'cc_parent'=> 'Родительская категория'
    		);
    }


    public function getParent(){
        return $this->hasOne(ClientCategory::className(),['cc_id'=>'cc_parent']);
    }

}