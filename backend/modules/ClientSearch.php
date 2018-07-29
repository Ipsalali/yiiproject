<?php

namespace backend\modules;

use common\models\Client;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use frontend\modules\FilterModelBase;
use common\models\PaymentState;
use frontend\models\CustomerPayment;
use yii\db\Query;

class ClientSearch extends Client
{
    /**
     * Принимаемые моделью входящие данные
     */

    public $page_size = 10;


    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['manager','client_category_id','ipay'],'integer']
        ];
    }

    public function scenarios(){
        return Client::scenarios();
    }


    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search($params)
    {   

        $query = Client::find();

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

        if($this->ipay){
            $subquery = new Query;
            $subquery->select("id")->from("client");
            $subsql1 = "(SELECT COUNT(DISTINCT a.id) FROM autotruck a 
                    INNER JOIN customer_payment cp ON a.id = cp.autotruck_id
                    INNER JOIN payment_state ps  ON ps.id = cp.payment_state_id
                    WHERE cp.client_id = client.id AND ps.end_state = 1)";

            $subsql2 = "(SELECT COUNT(DISTINCT a.id) FROM autotruck a
                INNER JOIN app ap ON ap.autotruck_id = a.id
                WHERE ap.client = client.id)";
            if(PaymentState::getDefaultState()->id == $this->ipay){
                $subquery->where("{$subsql1} != {$subsql2}");
            }elseif(PaymentState::getEndState()->id == $this->ipay){
               $subquery->where("{$subsql1} = {$subsql2}");
            }
            $query->andFilterWhere(["id"=>$subquery]);
        }

        // Если ошибок нет, фильтруем по цене
        $query->andFilterWhere([
                'manager' => $this->manager,
                'client_category_id' => $this->client_category_id,
                ]);

        
        return $dataProvider;
    }

}