<?php

namespace backend\modules;

/**
* 
*/
class PaymentStateFilter
{
	public static function getFilters(){
		$states =[];
		$ar = array("id"=>"none","title"=>"Не реализованные");
		array_push($states, $ar);
		return $states;
	}
}
?>