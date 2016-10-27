<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\Autotruck;
use common\models\User;

/**
*
*
*
*/

class PaymentsExpenses extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['manager_id','sum'], 'required'],
            ['sum','double']
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
		return '{{%payments_expenses}}';
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
    		'id'=>'Номер',
            'manager_id'=>'Менеджер',
            'sum'=>'Сумма ($)',
            'date'=>'Дата',
            'comment'=>'Комментарии'
    		);
    }


    public function getManager(){
        return $this->hasOne(User::className(),["id"=>'manager_id']);
    }


}