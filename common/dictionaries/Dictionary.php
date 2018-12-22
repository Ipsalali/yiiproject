<?php


namespace common\dictionaries;


abstract class Dictionary{
	/**
	* array
	*/
	protected static $labels = array();
	protected static $delimiter=";";
    public static function getDelimiter(){
        return self::$delimiter;
    }


	public static function getLabels($code = null){
        if($code && array_key_exists($code, static::$labels))
            return static::$labels[$code];
        return static::$labels;
    }


    public static function toString(array $select = array()){
    	if(count($select)){
    		$selected = array();
    		foreach ($select as $code) {
    			if(array_key_exists($code, static::$labels))
    				$selected[]=static::$labels[$code];
    		}
    	}else{
    		$selected = static::$labels;
    	}
    	return mb_strtolower(implode(static::$delimiter, $selected));
    }


    public static function toArrayCode($str = null){
        $codes = array();
        
        if(!empty($str)){
            $str = mb_strtolower($str);
            $select = explode(static::$delimiter, $str);
            $nls = static::normalizeLabels();
            foreach ($select as $value) {
                $key = array_search($value, $nls);
                if($key !== false)
                    array_push($codes, $key);
            }
        }
        return $codes;
    }


    public static function getCode($str = null){
        if($str == null) return null;
        $str = mb_strtolower($str);
        
        $nls = static::normalizeLabels();
        
        return array_search($str, $nls);
    }


    public static function normalizeLabels(){
        $labels = [];
        foreach (static::$labels as $key => $value) {
            $labels[$key] = mb_strtolower($value);
        }
        return $labels;
    }
}