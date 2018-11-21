<?php 

namespace console\models;

class Permissions{
	

	public static function getPerms(){	
		return array(
			'autotruck/addexpenses'=> 'Добавление расхода',
			'Autotruck/create'=> 'Добавление заявки',
			'Autotruck/delete'=> 'Удаление заявки',
			'Autotruck/index'=> 'Список заявок',
			'Autotruck/read'=> 'Просмотр заявки',
			'autotruck/report'=> 'Отчет просмотр',
			'Autotruck/update'=> 'Редактирование заявок',
			'client/autotruckpayment'=> 'Смена статуса оплаты',
			'client/check'=> 'Сформировать чек',
			'Client/create'=>'Право добавлять клиента',
			'Client/delete'=>'Удаление клиента',
			'Client/index'=>'Список клиентов',
			'client/mycheck'=>'Сформировать для себя счет',
			'Client/profile'=>'Профиль клиента',
			'Client/read'=>'Просмотр клиента',
			'Client/update'=>'Редактирование клиента',
			'Post/create'=>'Создание новостей',
			'Post/delete'=>'Удаление новости',
			'Post/index'=>'Страница новостей',
			'Post/read'=>'Просмотр новости',
			'Post/update'=>'Редактирование новости',
			'sellers'=>'Раздел поставщики',
			'sender'=>'Раздел отправители',
			'site/sverka'=>'Просмотр сверки',
			'sverka/addpaymentsmanager'=>'Добавление оплаты в сверке по доставкам',
			'sverka/remove-client-pay-by-transfer'=>'Удаление оплаты из сверки по переводам',
			'sverka/removepayajax'=> 'Удаление оплаты из сверки по доставкам',
			'sverka/save-client-payment-transfer'=> 'Добавление оплаты в сверке по переводам',
			'transferspackage'=> 'Раздел переводы',
			'import/index'=>'Раздел импорта',
			'read/app/rate'=>'Просмотр колонки "ставка" в заявках',
			'read/app/sum_us'=>'Просмотр колонки "сумма ($)" в заявках',
			'read/app/sum_ru'=>'Просмотр колонки "сумма (руб)" в заявках'
		);
	}
}