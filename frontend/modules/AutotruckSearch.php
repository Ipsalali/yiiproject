<?php

namespace frontend\modules;

use frontend\models\Autotruck;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use frontend\modules\FilterModelBase;

class AutotruckSearch extends Autotruck
{
    /**
     * Принимаемые моделью входящие данные
     */

    public $date_from;
    public $date_to;
    public $page_size = 15;
    public $filterPosition = FILTER_POS_BODY;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            // Обязательное поле
            ['id','integer'],
            ['course','double'],
            // Только числа, значение как минимум должна равняться единице
            [['date','date_from','date_to','country','status','name'],'safe']
        ];
    }

    public function scenarios(){
        return Autotruck::scenarios();
    }

    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search($params)
    {
        // Создаём запрос на получение продуктов вместе категориями
        $query = Autotruck::find();

        /**
         * Создаём DataProvider, указываем ему запрос, настраиваем пагинацию
         */
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => new Pagination([
                    'pageSize' => $this->page_size
                ])
        ]);
       
        if(!($this->load($params) && $this->validate())){
            return $dataProvider;
        }
        
        // Если ошибок нет, фильтруем по цене
        $query->andFilterWhere([
                'country' => $this->country,
                'status' => $this->status,
                'course' => $this->course,
                ]);
        
        if($this->date_from)
            $query->andFilterWhere(['>=', 'date', date("Y.m.d H:i:s",strtotime($this->date_from))]);

        if($this->date_to)
            $query->andFilterWhere(['<=', 'date', date("Y.m.d H:i:s",strtotime($this->date_to))]);

        $query->andFilterWhere(['like','name',$this->name]);
        

        return $dataProvider;
    }

    
}