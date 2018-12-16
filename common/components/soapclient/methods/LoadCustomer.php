<?php

namespace soapclient\methods;
 
 
class LoadCustomer extends BaseMethod{

	public $guid;
	public $name;
	public $email;

	public function rules(){
		return [
			[['email'],'required'],
			[['guid','name'],'string']
		];
	}



	public function getParameters(){
		return [
			'Контрагент'=>$this->attributes
		];
	}

}