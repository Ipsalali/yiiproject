<?php

namespace WSUserActions\models;

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

    const EVENT_UPDATE = 'update';

	public function rules(){
		return [
            [['table_name','record_id','user_id'], 'required'],
            [['user_id','record_id'],'integer'],
            [['start_at'],'default','value'=>date('Y-m-d\TH:i:s',time())],
            [['start_at','finish_at'],'filter','filter'=>function($v){
                    return $v ? date('Y-m-d\TH:i:s',strtotime($v)) : null;
                }
            ],
            ['event','default','value'=>self::EVENT_UPDATE],
            [['active'],'default','value'=>1],
            ['record_id','checkExsistActive']
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
    		'table_name'=>'Ресурс',
            'record_id'=>'Номер записи',
    		'user_id'=>'Пользователь',
    		'start_at'=>'Время начала действии',
            'finish_at'=>'Время окончания действии',
    		'active'=>'Состояние',
    	);
    }


    public function checkExsistActive($attribute,$params){
        if (!$this->hasErrors()){
            $a = self::isBusyAction([
                'table_name'=>$this->table_name,
                'record_id'=>$this->record_id,
            ]);

            if($a){
                $this->addError($attribute,"Данная запись открыта на редактирование другим пользователем");
            }
        }
    }

    public static function isBusyAction($data){
        $a = self::find()->where([
                'active'=>1,
                'table_name'=>$data['table_name'],
                'record_id'=>$data['record_id'],
                'event'=>self::EVENT_UPDATE,
        ])->one();

        return isset($a->id) && $a->id ? $a : false;
    }


    public static function register($data){
        $action = new self;

        //$localsocket = 'tcp://127.0.0.1:1234';

        if($action->load(['UserAction'=>$data]) && $action->validate()){

            // connect to a local tcp-server
            //$instance = stream_socket_client($localsocket);

            // send message
            // fwrite($instance, json_encode([
            //     'table_name'=>$action->table_name,
            //     'record_id'=>$action->record_id,
            //     'user_id'=>$action->user_id,
            //     'event'=>$action->event
            // ])  . "\n");

            $action->save();
        }

        return $action;
    }


    public static function close($data){
        
        $table_name = isset($data['table_name']) ? $data['table_name'] : null;
        $record_id = isset($data['record_id']) ? (int)$data['record_id'] : null;
        $user_id = isset($data['user_id']) ? $data['user_id'] : null;

        if(!$table_name || !$record_id || !$user_id) return false;

        return Yii::$app->db->createCommand()->update(self::tableName(),['active'=>0,'finish_at'=>date("Y-m-d\TH:i:s",time())]," table_name=:table_name AND record_id=:record_id AND event=:event AND user_id=:user_id AND active=1")
            ->bindValue(':table_name',$table_name)
            ->bindValue(':record_id',$record_id)
            ->bindValue(':user_id',$user_id)
            ->bindValue(':event',self::EVENT_UPDATE)->execute();
    }



    public static function deleteAction($data){
        
        $table_name = isset($data['table_name']) ? $data['table_name'] : null;
        $record_id = isset($data['record_id']) ? (int)$data['record_id'] : null;
        $user_id = isset($data['user_id']) ? $data['user_id'] : null;

        if(!$table_name || !$record_id || !$user_id) return false;

        return Yii::$app->db->createCommand()->delete(self::tableName()," table_name=:table_name AND record_id=:record_id AND event=:event AND user_id=:user_id AND active=1")
            ->bindValue(':table_name',$table_name)
            ->bindValue(':record_id',$record_id)
            ->bindValue(':user_id',$user_id)
            ->bindValue(':event',self::EVENT_UPDATE)->execute();
    }
}