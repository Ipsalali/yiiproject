<?php

namespace soapclient\methods;
 
 
class Calcsquare extends BaseMethod{

	public $lineguid;
	public $length;
	public $count;


	public function rules(){
		return [
			[['lineguid','length'],'required'],
			['lineguid','string','min'=>0,'max'=>36],
			[['length','count'],'number'],
			['count','default','value'=>null]
		];
	}

	


	public function getParameters(){
		return [
			'parameters'=>$this->attributes
		];
	}
}