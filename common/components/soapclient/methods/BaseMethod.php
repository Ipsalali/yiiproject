<?php

namespace soapclient\methods;
 
use yii\base\Model;
 
abstract class BaseMethod extends Model implements MethodInterface{


	public function save(){
		return true;
	}
}