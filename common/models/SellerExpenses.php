<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\Autotruck;
use common\models\User;
use common\models\Seller;

/**
*
*
*
*/

class SellerExpenses extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['seller_id','sum','package_id','date'], 'required'],
            ['sum','double'],
            [['package_id','seller_id'],"integer"],
            ['comment','default','value'=>null],
            [['date'],'filter','filter'=>function($v){ 
                if(is_integer($v)){
                    return date("Y-m-d H:i:s",$v);
                }else{
                    return date("Y-m-d H:i:s",strtotime($v));
                }
            }],
            [['date'],'default','value'=>date("Y-m-d H:i:s",time())],
            [['comment'],'filter','filter'=>function($v){ return trim(strip_tags($v));}],
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
		return '{{%expenses_seller}}';
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
    		'package_id'=>'Перевод',
            'seller_id'=>'Поставщик',
            'sum'=>'Сумма',
            'comment'=>'Комментарии',
            'date'=>'Дата'
    		);
    }
    
    
    public function getSeller(){
        if($this->seller_id)
            return Seller::getSellers($this->seller_id);
    }
}