<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use backend\models\App;
use backend\models\Autotruck;
use common\models\Client;

/**
*
*
*
*/

class AutotruckNotification extends ActiveRecord
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
		return '{{%autotruck_notification}}';
	}

	/**
     * @return array primary key of the table
     **/     
    public static function primaryKey(){
    	return array('nid');
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'nid'=>'Номер',
    		'autotruck_id'=>'Заявка',
            'status_id'=>'Статус',
    		'client_id'=>'Клиент',
            'app_id' => 'Наименование'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(Autotrauck::className(),["id"=>'autotruck_id']);
    }

    public function getStatus(){
        return $this->hasOne(Status::className(),["id"=>'status_id']);
    }

    public function getClient(){
        return $this->hasOne(Client::className(),["id"=>'client_id']);
    }

    public function getApp(){
        return $this->hasOne(App::className(),["id"=>'app_id']);
    }

}