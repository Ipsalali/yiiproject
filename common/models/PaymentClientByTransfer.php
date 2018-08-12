<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\Transfer;
use common\models\TransfersPackage;
use common\models\User;
use common\models\Seller;
use common\models\Client;
use common\models\Organisation;
use common\base\ActiveRecordVersionable;
/**
*
*
*
*/

class PaymentClientByTransfer extends ActiveRecordVersionable
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['client_id','sum_ru'], 'required'],
            [['sum','course','sum_ru'],'double'],
            ['date','filter','filter'=>function($d){ 
                return date("Y-m-d\TH:i:s",$d ? strtotime($d) : time());
            }],
            [['currency','contractor_org','contractor_seller'],"integer"],
            ['comment','filter','filter'=>function($v){return trim(strip_tags($v));}],
            ['date','default','value'=>date("Y-m-d\TH:i:s",time())],
        ];
	}


    public static function versionableAttributes(){
        return [
            'date',
            'client_id',
            'currency',
            'course',
            'sum',
            'sum_ru',
            'contractor_org',
            'contractor_seller',
            'comment',
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
		return '{{%payment_client_by_transfer}}';
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
            'date'=>"Время",
            'client_id'=>'Клиент',
            'currency'=>"Валюта",
            'sum'=>'Сумма',
            'course'=>'Курс',
            'sum_ru'=>'Дата',
            'contractor_org'=>'Организация',
            'contractor_seller'=>'Поставшик',
            'comment'=>'Комментарии',
    		);
    }




    public function getClient(){
        return $this->hasOne(Client::className(),["id"=>'client_id']);
    }




    public function getContractor(){

    }

    public function getContractors(){
        $contragents = array();

        $orgs = Organisation::find()->asArray()->all();
        $sellers = Seller::getSellers();

        foreach ($orgs as $o) {
            $contragents['organisation#'.$o['id']] = $o['org_name'];
        }

        foreach ($sellers as $s) {
            $contragents['seller#'.$s['id']] = $s['name'];
        }


        return $contragents;
    }
}