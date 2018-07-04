<?php

namespace frontend\modules;

use common\models\TransfersPackage;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\db\Query;

class TransferspackageFilter extends TransfersPackage
{
    /**
     * Принимаемые моделью входящие данные
     */

    public $page_size = 100;

    public $date_from;
    public $date_to;
    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['name','date_from','date_to','status','currency'],'safe']
        ];
    }

    public function scenarios(){
        return TransfersPackage::scenarios();
    }


    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search($params)
    {   

        $query = TransfersPackage::find();
        $query->orderBy(['name' => SORT_ASC]);
        $query->where(['isDeleted'=>0]);
        /**
             * Создаём DataProvider, указываем ему запрос, настраиваем пагинацию
             */
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
                'pagination' => new Pagination([
                    'pageSize' => $this->page_size
                ])
            ]);

        //если данные не фильтра не переданы или переданы не валидные данныеы
        if(!($this->load($params) && $this->validate())){

            return $dataProvider;
        }


        if($this->name){
            $query->andWhere("name LIKE '{$this->name}%'");
        }
        
        
        
        if($this->date_from){
            $query->andWhere([">=","`date`",date("Y-m-d H:i:s",strtotime($this->date_from))]);
        }
        
        
        if($this->date_to){
            $query->andWhere(["<=","`date`",date("Y-m-d H:i:s",strtotime($this->date_to))]);
        }
        
        if($this->currency){
            $query->andWhere(['currency'=>$this->currency]);
        }
        
        if($this->status){
            $query->andWhere(['status'=>$this->status]);
        }


        

        
        
        return $dataProvider;
    }

}