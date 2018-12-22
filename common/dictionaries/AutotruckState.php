<?php

namespace common\dictionaries;



class AutotruckState extends Dictionary{
	
	const CREATED = 1;
	const TO_EXPORT = 2;
	const EXPORTED= 3;

	protected static $labels = array(
		self::CREATED=>"Создан",
		self::TO_EXPORT=>"К выгрузке",
		self::EXPORTED=>"Выгружен",
	); 



	public static $notification = array(
		self::CREATED=>"default",
		self::TO_EXPORT=>"info",
		self::EXPORTED=>"success",
	); 
}