<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;
use yii\db\Command;
use common\models\Payment;

/**
*
*
*
*/

class Organisation extends ActiveRecord
{


    const PAY_CARD = 0;
    const PAY_CASH = 1;

    static $pay_labels = [
        self::PAY_CARD => "Безнал",
        self::PAY_CASH => "Наличными"
    ];

    const SCENARIO_CARD = "typr_card";
    const SCENARIO_CASH = "typr_cash";

	public function rules(){
		return [
            // name, email, subject and body are required
            [['bank_name','bank_check','bik','inn','kpp','org_name','org_check','org_address','headman'], 'required'],
            [['bank_name','org_name','org_address','headman'],'string'],
            [['bank_check','bik','inn','kpp','org_check'],'string'],
            ['description','string'],
            ['payment','default','value'=>self::PAY_CARD]
        ];
	}




    public function scenarios(){
        return array_merge(parent::scenarios(),[
            self::SCENARIO_CASH=>['org_name','description','payment'],
        ]);
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

    public function getPaymentLabel(){
        return self::$pay_labels[$this->payment];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Id',
    		'bank_name'=>'Наименование банка',
    		'bank_check'=>'Кор.счет',
    		'bik'=>'БИК',
            'inn'=>'ИНН',
            'kpp'=>'КПП',
            'org_name'=>'Наименование организации',
            'org_check'=>'Расчетный счет',
            'org_address'=>'Адрес организации',
            'active'=>'Действующий',
            'is_stoped'=>'Приостановлен',
            'headman'=>'Руководитель',
            'payment'=>'Способ оплаты',
            'description'=>'Описание'
    	);
    }

}