<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
/**
*
*
*
*/

class PaymentState extends ActiveRecord
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
		return '{{%payment_state}}';
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
            'color'=>'Цвет',
            'default'=>'По умолчанию'
    	);
    }

    public function setDefault($model){
        $sql = "UPDATE ".self::tableName()." SET `default`=0 WHERE id != ".$model->id.";";
        $sql .="UPDATE ".self::tableName()." SET `default`=1 WHERE id = ".$model->id.";";

        Yii::$app->db->createCommand($sql)->execute();
    }

    public static function getDefaultState(){

        return PaymentState::find()->where(['default' => 1])->one();
    }

}