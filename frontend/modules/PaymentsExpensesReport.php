<?php

namespace frontend\modules;

use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use frontend\models\PaymentsExpenses;
use common\models\User;
use common\models\Client;
use yii\db\Query;

class PaymentsExpensesReport extends PaymentsExpenses
{
    /**
     * Принимаемые моделью входящие данные
     */

    public $date_from;
    public $date_to;
    public $page_size = 100;
    public $manager_fullname = "";
    public $sum_search = "";
    
    public static $common_sum;
    public static $common_sum_card;
    public static $common_sum_cash;
    public static $select_toreport;
    
    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            [['date_from','date_to','payment','manager_id','sum_search','organisation','plus','manager_fullname','toreport'],'safe'],
        ];
    }

    public function scenarios(){
        return PaymentsExpenses::scenarios();
    }


    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search($params)
    {   

        $query = PaymentsExpenses::find();

        $query->andFilterWhere(['!=','manager_id',0]);

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

        //если данные не фильтра не переданы или переданы не валидные данныеы
        if(!($this->load($params) && $this->validate())){
            
            
            self::$select_toreport = 0;
            
            return $dataProvider;
        }
        
        
        
        if($this->manager_fullname){
            
            $query
            ->innerJoin(['cl'=>Client::tableName()],"cl.user_id = manager_id")
            ->andWhere(['cl.full_name'=>$this->manager_fullname]);
            
        }
        
        if($this->sum_search){
            
            if($this->toreport){
                if((int)$this->toreport == 1){
    			   $query->andWhere("sum LIKE '{$this->sum_search}%'");
                }elseif((int)$this->toreport == 2){
    				$query->andWhere("sum_cash LIKE '{$this->sum_search}%'");
    			}elseif((int)$this->toreport == 3){
    				$query->andWhere("sum_card LIKE '{$this->sum_search}%'");
    			} 
            }else{
                
                
                $query->andWhere("   
                    (
                        `toreport` = 1 AND   sum LIKE '{$this->sum_search}%'
                    )  OR
                    (
                        `toreport` = 2 AND   sum_cash LIKE '{$this->sum_search}%'
                    )  OR
                    (
                        `toreport` = 3 AND   sum_card LIKE '{$this->sum_search}%'
                    )
                ");
                
                
            }
            
            
        }
        
        
        if($this->date_from)
            $query->andFilterWhere(['>=','date', date("Y-m-d\TH:i:s",strtotime($this->date_from))]);

        if($this->date_to)
            $query->andFilterWhere(['<=','date', date("Y-m-d\TH:i:s",strtotime($this->date_to))]);


        if($this->organisation){
            $query->andFilterWhere(['organisation'=>$this->organisation]);
        }

        if(($this->payment == "0" || $this->payment > 0) && $this->payment !="none"){
            $query->andFilterWhere(['payment'=>$this->payment]);
        }
    
        if(isset($this->plus)){
            $query->andFilterWhere(['plus'=>$this->plus]);
        }
        
        if(isset($this->toreport)){
            
            self::$select_toreport = $this->toreport;
            
            $query->andFilterWhere(['toreport'=>$this->toreport]);
        }

        
       
        return $dataProvider;
    }
    
    
    
    
    public function getManagers(){
        
        $q = (new Query())->
                select(['us.id','cl.full_name','cl.name'])->
                from(['pe'=>PaymentsExpenses::tableName()])->
                innerJoin(['us'=>User::tableName()],"pe.manager_id = us.id")->
                innerJoin(['cl'=>Client::tableName()],"cl.user_id = us.id")->
                groupBy(['us.id'])->
                all();
                
        return $q;
    }



    

}