<?php

namespace backend\modules;

use common\models\Request;
use yii\db\Query;
use yii\data\Pagination;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class RequestSearch extends Request{
	 /**
     * Принимаемые моделью входящие данные
     */

    public $date_from;
    public $date_to;
    public $page_size = 100;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            // Только числа, значение как минимум должна равняться единице
            [['date_from','date_to','request','result','completed'],'safe'],

        ];
    }

    public function scenarios(){
        return Request::scenarios();
    }

    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search($params)
    {   
        $query = Request::find();
        //$query->orderBy(['id' => SORT_DESC]);
        /**
         * Создаём DataProvider, указываем ему запрос, настраиваем пагинацию
         */
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['id'=>SORT_DESC]],
            'pagination' => new Pagination([
                    'pageSize' => $this->page_size
                ])
        ]);
        
        if(!($this->load($params) && $this->validate())){

            return $dataProvider;
        }


        if($this->date_from)
            $query->andFilterWhere(['>=',self::tableName().'.created_at', date("Y-m-d\TH:i:s",strtotime($this->date_from))]);

        if($this->date_to)
            $query->andFilterWhere(['<=',self::tableName().'.created_at', date("Y-m-d\TH:i:s",strtotime($this->date_to))]);

        if($this->request !== null)
            $query->andFilterWhere([self::tableName().'.request' => $this->request]);

        if($this->result !== null)
            $query->andFilterWhere(['result'=>(int)$this->result]);

        if($this->completed !== null)
            $query->andFilterWhere(['completed'=>(int)$this->completed]);

        

        return $dataProvider;
    }




    public static function getRequestsNames(){
        return [
            'soapclient\methods\Calcsquare'=>'Calcsquare',
            'soapclient\methods\Useraccountload'=>'Useraccountload'
        ];
    }
}
?>