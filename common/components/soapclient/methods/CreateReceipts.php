<?php

namespace soapclient\methods;

 
class CreateReceipts extends BaseMethod{

	protected $parameters = [];


	public function setParameters(array $parameters){
		$this->parameters = $parameters;
	}


	public function getParameters(){
		

		$parameters = $this->parameters;

		return [
			'data_invoice'=>$parameters
		];
	}

}