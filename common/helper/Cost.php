<?php

namespace common\helper;


class Cost
{
	private static $nds = 20;

	public static function withNDS($price)
	{
		return $price / (100 + self::$nds) * self::$nds;
	}
}