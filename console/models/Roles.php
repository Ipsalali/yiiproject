<?php 

namespace console\models;

class Roles{
	

	public static function getRoles(){	
		return [
			'admin'=>'Администратор системы',
			'App_manager'=>'менеджер заявок',
			'client'=>'Клиент',
			'clientExtended'=>'Клиент с расширенными возможностями',
			'client_manager'=>'Менеджер клиентов',
			'expenses_manager'=>'Менеджер расходов',
			'main_manager'=>'Менеджер системы',
			'seller'=>'Поставщик',
			'sender_manager'=>'Менеджер отправителей',
			'transfer_manager'=>'Менеджер переводов',
		];
	}
}