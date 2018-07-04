<?php

namespace backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use backend\models\Autotruck;
use common\models\User;

/**
*
*
*
*/

class ExpensesManager extends ActiveRecord
{


	public function rules(){
		return [
            // name, email, subject and body are required
            [['manager_id','cost','autotruck_id'], 'required'],
            ['cost','double']
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
		return '{{%expenses_manager}}';
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
            'manager_id'=>'Менеджер',
            'cost'=>'Сумма ($)',
            'comment'=>'Комментарии'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(Autotruck::className(),["id"=>'autotruck_id']);
    }

    public function getManager(){
        return $this->hasOne(User::className(),["id"=>'manager_id']);
    }

    public static function getAutotruckExpenses($autotruck_id){
        return ExpensesManager::find()->where(['autotruck_id'=>$autotruck_id])->all();
    }

}