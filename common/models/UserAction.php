<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
*
*
*
*/
class UserAction extends ActiveRecord
{


	public function rules(){
		return [
            [['table','record_id','user_id'], 'required'],
            [['user_id','record_id'],'integer'],
            [['start_at','finish_at'],'filter','filter'=>function($v){
                    return $v ? date('Y-m-d\TH:i:s',strtotime($v)) : date('Y-m-d\TH:i:s',time());
                }
            ],
            [['active'],'default','value'=>1]
        ];
	}


	

	/**
     * @return string the associated database table name
     */
	public static function tableName(){
		return '{{%user_actions}}';
	}

	


    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels(){
    	return array(
    		'id'=>'Номер',
    		'table'=>'Ресурс',
            'record_id'=>'Номер записи',
    		'user_id'=>'Пользователь',
    		'start_at'=>'Время начала действии',
            'finish_at'=>'Время окончания действии',
    		'active'=>'Состояние',
    	);
    }
}