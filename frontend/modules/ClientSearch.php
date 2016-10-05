<?php

namespace frontend\modules;

use common\models\Client;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use frontend\modules\FilterModelBase;

class ClientSearch extends FilterModelBase
{
    /**
     * Принимаемые моделью входящие данные
     */
    public $client_category_id;
    public $page_size = 20;

    public $filterPosition = FILTER_POS_BODY;

    /**
     * Правила валидации модели
     * @return array
     */
    public function rules()
    {
        return [
            // Обязательное поле
            ['client_category_id','required'],
            // Только числа, значение как минимум должна равняться единице
            //['page_size', 'integer', 'integerOnly' => true, 'min' => 1]
        ];
    }

    /**
     * Реализация логики выборки
     * @return ActiveDataProvider|\yii\data\DataProviderInterface
     */
    public function search()
    {
        // Создаём запрос на получение продуктов вместе категориями
        $query = Client::find()
            ->innerJoin('client_category on (cc_id=client_category_id)');

        /**
         * Создаём DataProvider, указываем ему запрос, настраиваем пагинацию
         */
        $this->_dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => new Pagination([
                    'pageSize' => $this->page_size
            ])
        ]);

        // Если ошибок нет, фильтруем по цене
        if ($this->validate() && $this->client_category_id) {
            $query->where('client_category_id = :category', [':category' => $this->client_category_id]);
        }

        return $this->_dataProvider;
    }

    /**
     * Переопределяем метод компоновки моделей,
     * возвращаем так же категории
     * Это синтетический пример.
     * @return array|mixed
     */
    public function buildModels()
    {
        $result = [];

        /**
         * @var shop\models\Product $product
         */
        foreach ($this->_dataProvider->getModels() as $client) {
            $result[] = array_merge($client->getAttributes(), [
                    'categories' => $client->clientCategory
                ]);
        }

        return $result;
    }
}