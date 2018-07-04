<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use backend\models\App;
use common\models\Status;

/**
*
*
*
*/

class AppTrace extends ActiveRecord
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
		return '{{%app_trace}}';
	}

	/**
     * @return array primary key of the table
     **/     
    public static function primaryKey(){
    	return array('trace_id');
    }


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'trace_id'=>'Номер',
    		'autotruck_id'=>'Заявка',
            'status_id'=>'Статус',
    		'traсe_first'=>'Первое состояние',
    		'traсe_last'=>'Последнее состояние',
    		'prevstatus_id'=>'Предыдущее состояние',
            'trace_date'=>'Дата'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(App::className(),["id"=>'autotruck_id']);
    }

    public function getStatus(){
        return $this->hasOne(Status::className(),["id"=>'status_id']);
    }

    public function getPrevStatus(){
        return $this->hasOne(Status::className(),["id"=>'prevstatus_id']);
    }

}