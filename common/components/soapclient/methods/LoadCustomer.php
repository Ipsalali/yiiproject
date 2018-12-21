<?php

namespace soapclient\methods;
 
 
class LoadCustomer extends BaseMethod{

	public $guid;
	public $name;
	public $email;

	public function rules(){
		return [
			[['name'],'required'],
			[['guid','name','email'],'string']
		];
	}



	public function getParameters(){
		return [
			'data_company'=>$this->attributes
		];
	}

}