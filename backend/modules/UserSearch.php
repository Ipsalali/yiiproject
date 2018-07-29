<?php

namespace backend\modules;

use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use backend\models\User;
use yii\db\Query;

class UserSearch extends User
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
            [['id','name','email','phone','username'],'safe']
        ];
    }

    public function scenarios(){
        return User::scenarios();
    }


    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search($params)
    {   

        $query = User::find();

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

        if($this->id){
            $query->andWhere(['id'=>$this->id]);
        }else{
            if($this->name){
                $query->andWhere("`name` LIKE '{$this->name}%'");
            }

            if($this->username){
                $query->andWhere("`username` LIKE '{$this->username}%'");
            }

            if($this->phone){
                $query->andWhere("`phone` LIKE '{$this->phone}%'");
            }

            if($this->email){
                $query->andWhere("`email` LIKE '{$this->email}%'");
            }
        }

        

        
        return $dataProvider;
    }

}