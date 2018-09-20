<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\Autotruck;
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
            [['autotruck_id','status_id'], 'required'],
            [['traсe_first','traсe_last','prevstatus_id'],'integer'],
            [['trace_date'],'filter','filter'=>function($v){
                                                            return $v ? date('Y-m-d\TH:i:s',strtotime($v)) : date('Y-m-d\TH:i:s',time());
                                                        }
            ],
            [['traсe_first','prevstatus_id'],'default','value'=>0],
            [['traсe_last'],'default','value'=>1],
            ['autotruck_id','refreshIfExists']
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
    		'autotruck_id'=>'Номер заявки',
            'status_id'=>'Статус',
    		'traсe_first'=>'Первое состояние',
    		'traсe_last'=>'Последнее состояние',
    		'prevstatus_id'=>'Предыдущее состояние',
            'trace_date'=>'Дата'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(Autotruck::className(),["id"=>'autotruck_id']);
    }

    public function getStatus(){
        return $this->hasOne(Status::className(),["id"=>'status_id']);
    }

    public function getPrevStatus(){
        return $this->hasOne(Status::className(),["id"=>'prevstatus_id']);
    }



    public function checkExistsStatus($attribute,$params){
        if (!$this->hasErrors()) {
            $result = self::find()->where(['status_id'=>$this->status_id,'autotruck_id'=>$this->autotruck_id])->one();
            if ($result && isset($result['trace_id']) && $result['trace_id']) {
                $this->addError($attribute, 'Трасировка для этой заявки с таким статусом уже существует!');
            }
        }
    }


    /**
    *
    *
    */
    public function refreshIfExists($attribute,$params){
        if (!$this->hasErrors()) {
            $result = self::find()->where(['status_id'=>$this->status_id,'autotruck_id'=>$this->autotruck_id])->one();
            if ($result && isset($result['trace_id']) && $result['trace_id']) {
                $result->trace_date = $this->trace_date;
                $result->traсe_first = $this->traсe_first;
                $result->traсe_last = $this->traсe_last;
                $result->prevstatus_id = $this->prevstatus_id;
                $result->save(false);//Отлючаем проверку данных, иначе возникнет бесконечный цикл
                $this->addError($attribute, 'Трасировка для этой заявки с таким статусом уже существует!');
            }
        }
    }




    public static function addSelf($params){
        $trace = new self();
        if(!array_key_exists("AppTrace", $params))
            $data = ['AppTrace'=>$params];
        else
            $data = $params;
        
        
        $trace->load($data);
        $trace->save();
        return $trace;
    }


}