<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\Client;
/**
*
*
*
*/

class ClientOrganisation extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['client_id','organisation_id'], 'required']
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
		return '{{%client_organisation}}';
	}

	/**
     * @return array primary key of the table
     **/     
    public static function primaryKey(){
    	return array('client_id','organisation_id');
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'client_id'=>'Клиент',
    		'organisation_id'=>'Организация',
            'relation_number'=>'Номер договора'
    		);
    }



    public static function findByClienAndOrg($client,$org){
        return self::findOne(['client_id'=>$client,'organisation_id'=>$org]);
    }


    public static function saveRelation(Client $client,$number = false){
        if(!$client instanceof Client || !$client->organisation_pay_id)
            return false;

        $relation = self::findByClienAndOrg($client->id,$client->organisation_pay_id);
        if(isset($relation->client_id) && $relation->client_id){
            $relation->relation_number =$number ? $number : $client->contract_number;
        }else{
            $relation = new ClientOrganisation;
            $relation->client_id = $client->id;
            $relation->relation_number = $number ? $number : $client->contract_number;
            $relation->organisation_id = $client->organisation_pay_id;
        }

        return $relation->save();
    }

}