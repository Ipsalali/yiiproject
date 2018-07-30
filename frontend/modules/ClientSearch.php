<?php

namespace frontend\modules;

use common\models\Client;
use yii\data\ArrayDataProvider;
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

    public $page_size = 0;

    public static $total_sverka = 0;

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

        // $query = Client::find();
        // $query->orderBy(['name' => SORT_ASC]);
       
         

        $conditions = array();
        $conditions[] = "`isDeleted` = 0";
        //если данные не фильтра не переданы или переданы не валидные данныеы
        if($this->load($params) && $this->validate()){

            if($this->manager)
                array_push($conditions, "`manager` = {$this->manager}");

            if($this->client_category_id)
                array_push($conditions, "`client_category_id` = {$this->client_category_id}");
        }   

        

        // if($this->ipay){
        //     $subquery = new Query;
        //     $subquery->select("id")->from("client");
        //     $subsql1 = "(SELECT COUNT(DISTINCT a.id) FROM autotruck a 
        //             INNER JOIN customer_payment cp ON a.id = cp.autotruck_id
        //             INNER JOIN payment_state ps  ON ps.id = cp.payment_state_id
        //             WHERE cp.client_id = client.id AND ps.end_state = 1)";

        //     $subsql2 = "(SELECT COUNT(DISTINCT a.id) FROM autotruck a
        //         INNER JOIN app ap ON ap.autotruck_id = a.id
        //         WHERE ap.client = client.id)";
        //     if(PaymentState::getDefaultState()->id == $this->ipay){
        //         $subquery->where("{$subsql1} != {$subsql2}");
        //     }elseif(PaymentState::getEndState()->id == $this->ipay){
        //        $subquery->where("{$subsql1} = {$subsql2}");
        //     }
        //     $query->andFilterWhere(["id"=>$subquery]);
        // }

        // Если ошибок нет, фильтруем по цене
        // $query->andFilterWhere([
        //         'manager' => $this->manager,
        //         'client_category_id' => $this->client_category_id,
        //         ]);

        $condition = is_array($conditions) && count($conditions) ? "WHERE ".implode(" AND ", $conditions) : "";
        //$sql = "CALL get_client_list('{$condition}',{$this->page_size}, 1)";
        $sql = "SELECT id,name,user_email,manager_name,phone,category_title,sverka_sum,user_id FROM client_list ".$condition." ORDER BY name ASC";
        $connection = \Yii::$app->getDb();
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $result,
            'pagination' => new Pagination([
                'pageSize' => $this->page_size
            ])
        ]);
        
        return $dataProvider;
    }

}