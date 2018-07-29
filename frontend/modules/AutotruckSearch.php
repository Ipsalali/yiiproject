<?php

namespace frontend\modules;

use frontend\models\Autotruck;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use frontend\modules\FilterModelBase;
use common\models\PaymentState;
use frontend\models\CustomerPayment;
use frontend\modules\PaymentStateFilter;
use yii\db\Query;

class AutotruckSearch extends Autotruck
{
    /**
     * Принимаемые моделью входящие данные
     */

    public $date_from;
    public $date_to;
    public $implements_state;
    public $page_size = 50;
    public $filterPosition = 'FILTER_POS_BODY';
    public $common_weight;
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
            [['date','date_from','date_to','country','decor','status','name','implements_state','auto_number','auto_name','common_weight'],'safe']
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

        $user = \Yii::$app->user->identity;
        $u_countries = \yii\helpers\ArrayHelper::map($user->accessCountry,'country_id','country_id');
        // Создаём запрос на получение продуктов вместе категориями
        $query = Autotruck::find()->where(["in",'country',$u_countries]);

        $query->where(['isDeleted'=>0]);
        
        $query->orderBy(['date' => SORT_DESC]);
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
            }elseif($this->implements_state == PaymentStateFilter::STATE_NONE){
                
                //Если существуют наименования без клиентов в заявке(т.е нереализованные)
                $query->where("EXISTS (SELECT a.id FROM app a WHERE a.autotruck_id = autotruck.id AND a.client =0)");
            }elseif($this->implements_state == PaymentStateFilter::STATE_STOCK){
                
                //Если существуют наименования без клиентов в заявке(т.е нереализованные)
                $query->where("EXISTS (SELECT a.id FROM app a WHERE a.autotruck_id = autotruck.id AND a.out_stock=0)");
                $this->status = 3;
            }

            
        }
        
        // Если ошибок нет, фильтруем по цене
        $query->andFilterWhere([
                'status' => $this->status,
                'course' => $this->course,
                ]);

        
        if($this->country && array_key_exists($this->country, $u_countries)){
            $query->andFilterWhere([
                'country' => $this->country,
                ]);

        }
        

        
        if($this->date_from)
            $query->andFilterWhere(['>=', 'date', date("Y.m.d H:i:s",strtotime($this->date_from))]);

        if($this->date_to)
            $query->andFilterWhere(['<=', 'date', date("Y.m.d H:i:s",strtotime($this->date_to))]);
        
        if($this->name)
            $query->andFilterWhere(['like','name',$this->name]);

        if($this->decor)
            $query->andFilterWhere(['like','decor',$this->decor]);
        
        if($this->auto_number)    
            $query->andFilterWhere(['like','auto_number',$this->auto_number]);
        
        if($this->auto_name)   
            $query->andFilterWhere(['like','auto_name',$this->auto_name]);
        

        if($this->common_weight){

            $sql = "SELECT SUM(aw.weight) as common_weight  FROM app aw
                WHERE aw.autotruck_id = autotruck.id AND aw.type = '0'";

            $query->where("({$sql}) LIKE '{$this->common_weight}%'");
        }
        
        
        
        return $dataProvider;
    }

    
}