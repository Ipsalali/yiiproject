<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

use common\models\Autotruck;
use common\models\Status;
use common\models\User;
use common\models\Client;
use common\models\Sender;
use common\models\TypePackaging;
use common\base\ActiveRecordVersionable;

/**
*
*
*
*/
class App extends ActiveRecordVersionable
{

    /**
    * boolean 
    */
    public $enabledPostVersionId = true;


	public function rules(){
		return array_merge(parent::rules(),[
            // name, email, subject and body are required
            [['info','autotruck_id'], 'required'],
            [['client','sender','package','count_place','autotruck_id'],'integer'],
            [['client','sender','package'],'default','value'=>null],
            [['count_place','rate'],'default','value'=>0],
            [['info','comment'],'filter','filter'=>function($v){return trim(strip_tags($v));}],
            [['rate','weight'],'number'],

            ['type','in','range'=>[0,1]],
            [['type','weight','imported'],'default','value'=>0],
            [['rate','summa_us'],'filter','filter'=>function($v){return round($v,2);}]
        ]);
	}


    public static function versionableAttributes(){
        return [
            'client',
            'weight',
            'rate',
            'summa_us',
            'status',
            'comment',
            'info',
            'autotruck_id',
            'type',
            'out_sock',
            'sender',
            'count_place',
            'package',
            'isDeleted'
        ];
    }

    public function load($data, $formName = null){
        
        if(parent::load($data, $formName)){

            if((int)$this->type){
                //У услуг не может быть веса.
                $this->weight = 1;
            }
            return true;
        }

        return false;
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
		return '{{%app}}';
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
    		'id'=>'Id',
    		'client'=>'Клиент',
            'sender'=>'Отправитель',
            'count_place'=>'Количество мест',
            'package'=>'Упаковка',
            'info'=>'Информация',
    		'weight'=>'Вес',
    		'rate'=>'Ставка',
    		'summa_us'=>'Сумма',
            'status'=>'Статус',
            'comment'=>'Комментарий',
            'autotruck_id'=>'Заявка',
            'type' => 'Тип',
            'out_stock' => 'Остаток на складе'
    		);
    }


    public function getAutotruck(){
        return $this->hasOne(Autotruck::className(),["id"=>'autotruck_id']);
    }

    public function getBuyer(){
        return $this->hasOne(Client::className(),["id"=>'client']);
    }

    public function getSenderObject(){
        return $this->hasOne(Sender::className(),["id"=>'sender']);
    }


    public function getTypePackaging(){
        return $this->hasOne(TypePackaging::className(),["id"=>'package']);
    }

    
    public function afterDelete(){
        parent::afterDelete();
    }

    public function getDescription(){
        $desc = "";
        $desc .= $this->sender ? $this->senderObject->name." " : "";
        $desc .= $this->count_place ? $this->count_place." " : "";
        $desc .= $this->package ? $this->typePackaging->title." " : "";
        $desc .= $this->info;
        
        return $desc;
    }


    public static function searchByKey($keyword){
        $query = new Query();
        
        $user = \Yii::$app->user->identity;
        $u_countries = \yii\helpers\ArrayHelper::map($user->accessCountry,'country_id','country_id');

        $where = "`info` LIKE '%$keyword%' OR `comment` LIKE '%$keyword%'";
        $query->select(['app.id','app.client','app.weight','app.rate','app.comment','app.info','app.autotruck_id'])
            ->from(self::tableName())
            ->innerJoin(Autotruck::tableName(),"autotruck.id = app.autotruck_id")
            ->where($where)
            ->andWhere(['in','country',$u_countries])
            ->limit(5);;
        
        return $query->all();
    }



    public function getHistory(){
        if(!$this->id) return false;

        return (new Query)
                ->select([
                    'rs.*',
                    'u.name as creator_name',
                    'u.username as creator_username',
                    'cl.name as client_name',
                    's.name as sender_name',
                    'tp.title as package_title'
                ])
                ->from(['rs'=>self::resourceTableName()])
                ->leftJoin(['u'=>User::tableName()]," rs.creator_id = u.id")
                ->leftJoin(['cl'=>Client::tableName()]," cl.id = rs.client")
                ->leftJoin(['s'=>Sender::tableName()]," s.id = rs.sender")
                ->leftJoin(['tp'=>TypePackaging::tableName()]," tp.id = rs.package")
                ->where([static::resourceKey()=>$this->id])
                ->orderBy(["rs.id"=>SORT_DESC])
                ->all();
    }
    

}