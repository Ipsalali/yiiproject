<?php

namespace backend\modules;

use backend\models\Autotruck;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use backend\modules\FilterModelBase;
use common\models\PaymentState;
use backend\models\CustomerPayment;
use backend\modules\PaymentStateFilter;
use yii\db\Query;

class AutotruckSearch extends Autotruck
{
    /**
     * Принимаемые моделью входящие данные
     */

    public $date_from;
    public $date_to;
    public $implements_state;
    public $page_size = 15;
    public $filterPosition = 'FILTER_POS_BODY';

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            // Обязательное поле
            ['id','integer'],
            // Только числа, значение как минимум должна равняться единице
            [['date','date_from','date_to','country','status','name','implements_state'],'safe']
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
        $query->orderBy(['id' => SORT_DESC]);
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

        if($this->implements_state){

            $subquery = new Query;
            $subquery->select("id")->from("autotruck");
            $subsql1 = "(SELECT COUNT(DISTINCT cp.id) FROM app a 
                    INNER JOIN customer_payment cp ON a.client = cp.client_id
                    INNER JOIN payment_state ps  ON ps.id = cp.payment_state_id
                    WHERE a.autotruck_id = autotruck.id AND ps.end_state = 1 AND cp.autotruck_id = autotruck.id)";

            //Количество клиентов в заявке
            $subsql2 = "(SELECT COUNT(DISTINCT c.id) FROM app a
                INNER JOIN client c ON a.client = c.id
                WHERE a.autotruck_id = autotruck.id)";

            if(PaymentStateFilter::getDefaultState()->id == $this->implements_state){
                $subquery->where("{$subsql1} != {$subsql2}");
                
                
                //Если не существуют наименования без клиентов в заявке
                $query->where("NOT EXISTS (SELECT a.id FROM app a WHERE a.autotruck_id = autotruck.id AND a.client = 0)");
                $query->andFilterWhere(["id"=>$subquery]);

            }elseif(PaymentStateFilter::getEndState()->id == $this->implements_state){
               $subquery->where("{$subsql1} = {$subsql2}");
              
               
               //Если не существуют наименования без клиентов в заявке
               $query->where("NOT EXISTS (SELECT a.id FROM app a WHERE a.autotruck_id = autotruck.id AND a.client = 0)");

                $query->andFilterWhere(["id"=>$subquery]);
            }elseif($this->implements_state == "none"){
                //print_r("none");
                //Если существуют наименования без клиентов в заявке(т.е нереализованные)
                $query->where("EXISTS (SELECT a.id FROM app a WHERE a.autotruck_id = autotruck.id AND a.client =0)");
            }

            
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