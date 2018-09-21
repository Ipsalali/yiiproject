<?php

namespace frontend\modules;
use common\models\PaymentState;

/**
* 
*/
class PaymentStateFilter
{	

	const STATE_NONE = "none";
	const STATE_STOCK = "in_stock";

	public static function getFilters(){
		$states = [];
		$ar = array("id"=>self::STATE_NONE,"title"=>"Не реализованные");
		array_push($states, $ar);
		$ar = array("id"=>self::STATE_STOCK,"title"=>"Остатки на складе");
		array_push($states, $ar);
		return $states;
	}
}
?>