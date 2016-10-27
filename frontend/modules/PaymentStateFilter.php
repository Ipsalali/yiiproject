<?php

namespace frontend\modules;
use common\models\PaymentState;

/**
* 
*/
class PaymentStateFilter extends PaymentState
{
	public static function getFilters(){
		$states = self::find()->where("`default` = '1' OR `end_state` = '1'")->asArray()->all();
		$ar = array("id"=>"none","title"=>"Не реализованные");
		array_push($states, $ar);
		return $states;
	}
}
?>