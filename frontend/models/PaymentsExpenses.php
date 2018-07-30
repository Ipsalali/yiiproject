<?php

namespace frontend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\Autotruck;
use common\models\User;
use common\models\Organisation;
use common\base\ActiveRecordVersionable;
/**
*
*
*
*/

class PaymentsExpenses extends ActiveRecordVersionable
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['manager_id','sum'], 'required'],
            ['sum','double'],
            [['payment','organisation'],'default','value'=>Organisation::PAY_CARD],
        ];
	}


    public static function versionableAttributes(){
        return [
            'manager_id',
            'sum',
            'date',
            'comment',
            'organisation',
            'payment',
            'sum_cash',
            'sum_card',
            'sum_cash_us',
            'plus',
            'toreport',
            'course',
            'isDeleted'
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
            'organisation'=>'Организация',
            'payment'=>'Способ оплаты',
            'comment'=>'Комментарии',
            'course'=>'Курс'
    		);
    }


    public function getManager(){
        return $this->hasOne(User::className(),["id"=>'manager_id']);
    }


    public function getOrg(){
        return $this->hasOne(Organisation::className(),["id"=>'organisation']);
    }

    public function getPaymentLabel(){
        return Organisation::$pay_labels[$this->payment];
    }

}