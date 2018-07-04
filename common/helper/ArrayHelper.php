<?php

namespace common\helper;

use common\helper\BaseArrayHelper;

class ArrayHelper extends BaseArrayHelper{



	public static function map($array, $from, $to, $group = null, $del = " ")
    {
        $result = [];


        foreach ($array as $element) {
            $key = static::getValue($element, $from);


            if(is_array($to) && count($to)){
            	$vs = [];
            	foreach ($to as  $tv) {
            		array_push($vs, static::getValue($element, $tv));
            	}
            	$value = implode($del, $vs);
            }else{
            	$value = static::getValue($element, $to);
            }
            
            if ($group !== null) {
                $result[static::getValue($element, $group)][$key] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }



    public static function mapForSelect($array,$from,$to,$prefix = "")
    {
        $result = [];

        foreach ($array as $element) {
            $key = static::getValue($element, $from);


            if(is_array($to) && count($to)){
            	$value = [];
            	foreach ($to as  $tv) {
            		$value[$prefix.$tv] = static::getValue($element, $tv);
            	}
            }else{
            	$value = static::getValue($element, $to);
            }
            
            
            $result[$key] = $value;
            
        }

        return $result;
    }


}
?>