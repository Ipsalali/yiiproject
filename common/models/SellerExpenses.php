<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use frontend\models\Autotruck;
use common\models\User;
use common\models\Seller;
use common\models\Currency;
use common\base\ActiveRecordVersionable;

use yii\db\Query;
/**
*
*
*
*/

class SellerExpenses extends ActiveRecordVersionable
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['seller_id','sum','package_id','date','currency','course'], 'required'],
            [['sum','sum_ru'],'double'],
            [['course'],'number'],
            [['package_id','seller_id'],"integer"],
            ['currency','in','range'=>[Currency::C_DOLLAR,Currency::C_EURO]],
            [['comment','currency','course'],'default','value'=>null],
            [['date'],'filter','filter'=>function($v){ 
                if(is_integer($v)){
                    return date("Y-m-d H:i:s",$v);
                }else{
                    return date("Y-m-d H:i:s",strtotime($v));
                }
            }],
            [['date'],'default','value'=>date("Y-m-d H:i:s",time())],
            [['isDeleted'],'default','value'=>0],
            [['comment','course'],'filter','filter'=>function($v){ return trim(strip_tags($v));}],

        ];
	}


    public static function versionableAttributes(){
        return [
            'package_id',
            'seller_id',
            'date',
            'sum',
            'sum_ru',
            'currency',
            'course',
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
		return '{{%seller_expenses}}';
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
            'currency'=>'Валюта',
            'course'=>'Курс',
            'sum'=>'Сумма',
            'sum_ru'=>'Сумма Руб',
            'comment'=>'Комментарии',
            'date'=>'Дата'
    		);
    }
    
    
    public function getSeller(){
        if($this->seller_id)
            return Seller::getSellers($this->seller_id);
    }


    public function getHistory(){
        if(!$this->id) return false;

        return (new Query)
                ->select([
                    'rs.*',
                    'u.name as creator_name',
                    'u.username as creator_username',
                    's.name as seller_name'
                ])
                ->from(['rs'=>self::resourceTableName()])
                ->leftJoin(['u'=>User::tableName()]," rs.creator_id = u.id")
                ->leftJoin(['s'=>Seller::tableName()]," s.id = rs.seller_id")
                ->where([static::resourceKey()=>$this->id])
                ->orderBy(["rs.id"=>SORT_DESC])
                ->all();
    }
}