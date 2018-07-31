<?php 

namespace common\models;

class Currency {

	const C_DOLLAR = 1;
    const C_EURO = 2;

    const C_DOLLAR_SYMBOL = "$";
    const C_EURO_SYMBOL = "€";


    protected static $currencyCodeTitle = [
        self::C_DOLLAR => self::C_DOLLAR_SYMBOL,
        self::C_EURO => self::C_EURO_SYMBOL
    ];


    public static function getCurrencyTitle($code = null){

        if(array_key_exists($code, self::$currencyCodeTitle)){
            return self::$currencyCodeTitle[$code];
        }else{
            return null;
        }
    }


    public static function getCurrencies(){
        return [
            [
                'id'=>self::C_DOLLAR,
                'title'=>self::C_DOLLAR_SYMBOL,
            ],
            [
                'id'=>self::C_EURO,
                'title'=>self::C_EURO_SYMBOL,
            ],
        ];
    }
}