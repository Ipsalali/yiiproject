<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use backend\models\Autotruck;
use common\models\PaymentState;
use common\models\Client;

/**
*
*
*
*/

class CustomerPayment extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            //[['info'], 'required']
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
		return '{{%customer_payment}}';
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
    		'autotruck_id'=>'Заявка',
            'payment_state_id'=>'Статус оплаты',
    		'client_id'=>'Клиент',
            'sum'=>'Сумма $',
            'date'=>'Дата',
            'comment'=>'Комментарии'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(Autotruck::className(),["id"=>'autotruck_id']);
    }

    public function getPaymentState(){
        return $this->hasOne(PaymentState::className(),["id"=>'payment_state_id']);
    }

    public function getClient(){
        return $this->hasOne(Client::className(),["id"=>'client_id']);
    }


    public static function getCustomerPayment($client,$autotruck){
        $state = CustomerPayment::find()->where("`client_id`=".(int)$client." AND `autotruck_id`=".(int)$autotruck)->one();

        return $state->id ? $state : new CustomerPayment;
    }

}