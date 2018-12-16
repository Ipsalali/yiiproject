<?php

namespace api\rest\modules;

use Yii;
use yii\db\Query;
use common\models\Autotruck;

class OrderFilter extends Autotruck{
    /**
     * Принимаемые моделью входящие данные
     */
    public $offset = 0;
    public $limit = 10;
    protected $client_id;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['limit','offset'],'integer']
        ];
    }

    public function scenarios(){
        return Autotruck::scenarios();
    }


    public function setClient($id){
    	$this->client_id = $id;
    }

    /**
     * Реализация логики выборки
     * @return Autotruck[]
     */
    public function filter($params)
    {   
    	if(!$this->client_id) return array();

    	$query = Autotruck::find()
        				->innerJoin('app','autotruck.id = app.autotruck_id AND app.client = '.$this->client_id)
        				->andWhere(['app.isDeleted'=>0,'autotruck.isDeleted'=>0])
        				->limit($this->limit)
        				->offset($this->offset)
        				->distinct()
        				->orderBy(["autotruck.date"=>SORT_DESC]);

    	if($this->load($params) && $this->validate()){
    		$query->limit($this->limit)->offset($this->offset);
    	}

        return $query->all();
    }

    
}