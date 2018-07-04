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

class Status extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['title'], 'required']
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
		return '{{%app_status}}';
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
            'description'=>'Описание',
            'notification_template'=> 'Шаблон уведомления',
            'send_check'=>'Отправлять счет',
            'sort' => 'Порядок'
    		);
    }

}