<?php

namespace frontend\modules;

use Yii;
use frontend\models\Autotruck;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use frontend\modules\FilterModelBase;
use common\models\PaymentState;
use frontend\models\CustomerPayment;
use frontend\modules\PaymentStateFilter;
use yii\db\Query;

class AutotruckReport extends Autotruck
{
    /**
     * Принимаемые моделью входящие данные
     */

    public $date_from;
    public $date_to;

    public $page_size = 100;


    public static $common_weight;
    public static $common_sum_us;
    public static $common_sum_ru;
    public static $common_expenses_ru;
    public static $total_common;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            // Только числа, значение как минимум должна равняться единице
            [['date_from','date_to','country','name'],'safe']
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

        // $user = \Yii::$app->user->identity;
        // $u_countries = \yii\helpers\ArrayHelper::map($user->accessCountry,'country_id','country_id');
        // // Создаём запрос на получение продуктов вместе категориями
        // $query = Autotruck::find()->where(["in",'country',$u_countries]);
        // $query->orderBy(['date' => SORT_DESC]);
        
        

        

        

        // /**
        //  * Создаём DataProvider, указываем ему запрос, настраиваем пагинацию
        //  */
        // $dataProvider = new ActiveDataProvider([
        //     'query' => $query,
        //     'pagination' => new Pagination([
        //             'pageSize' => $this->page_size
        //         ])
        // ]);
        
        
        
        $sqlCondition = " WHERE ap.`isDeleted`=0";
        if($this->load($params) && $this->validate()){
            $cs = [];

            if($this->date_from){
                $cs[] = "atr.`date` >= '".date("Y.m.d H:i:s",strtotime($this->date_from))."'";
                //$query->andFilterWhere(['>=', 'date', date("Y.m.d H:i:s",strtotime($this->date_from))]);
            }

            if($this->date_to){
                 $cs[] = "atr.`date` <= '".date("Y.m.d H:i:s",strtotime($this->date_to))."'";
                //$query->andFilterWhere(['<=', 'date', date("Y.m.d H:i:s",strtotime($this->date_to))]);
            }

            if($this->name){
                 $cs[] = "atr.`name` like '".$this->name."%'";
            }

            if($this->country){
                 $cs[] = "atr.`country_id` = ".$this->country;
            }


            if(count($cs)){
                $sqlCondition = " ".implode(" AND ", $cs)." ";
            }
        }

        $sql = "SELECT  atr.id, atr.name, atr.date,atr.country,atr.country_id, atr.course,
            SUM(case when ap.type = '0' then ap.weight else 0 end) as weight,
            SUM(case when ap.type = '0' then ap.weight*ap.rate else ap.rate end) as sum_us,
            SUM(case when ap.type = '0' then atr.course*ap.weight*ap.rate else atr.course*ap.rate end) as sum_ru,
            atr.expenses
        FROM `app` ap
            RIGHT JOIN (
                SELECT a.id, a.name, a.date,c.country, c.id as country_id, a.course,SUM(exp.cost) as expenses
                FROM `autotruck` as a 
                LEFT JOIN `expenses_manager` exp ON exp.autotruck_id = a.id 
                LEFT JOIN supplier_countries c ON c.id = a.country
                WHERE a.isDeleted = 0
                GROUP BY a.`id`
                ) atr ON ap.autotruck_id = atr.`id` 
                 ".$sqlCondition." 
        GROUP BY atr.`id` ORDER BY ap.id DESC";
       
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand($sql);

        $report =  $command->queryAll();
        

        $dataProvider = new ArrayDataProvider([
                'key'=>'id',
                'allModels' => $report,
                'sort' => [
                    'attributes' => ['id', 'name','course','country','weight','sum_us','sum_ru','expenses'],
                ],
                'pagination' => [
                    'pageSize' => $this->page_size,
                ],
        ]);
        
        
        
        
        
        
        return $dataProvider;
    }

    
}