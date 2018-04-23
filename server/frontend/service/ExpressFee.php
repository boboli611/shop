<?php

class ExpressFee {

    //首重
    static $first_price = [
        "青海" => 15,
        "新疆" => 15,
        "西藏" => 15,
        "内蒙" => 15,
        "台湾" => 36,
        "香港" => 30,
        "澳门" => 30,
    ];
    static $renew = [
        "青海" => 13,
        "新疆" => 13,
        "西藏" => 13,
        "内蒙" => 13,
        "台湾" => 28,
        "香港" => 12,
        "澳门" => 12,
    ];

    public static function sumPrice($address, $num) {
        
        $price = self::getFirstPrice($address) + self::getRenewPrice($address, $num - 1);
        return $price;
    }

    private static function getFirstPrice($address) {
        
        //海外300;
        return isset(self::$first_price[$address]) ? self::$first_price[$address] : 300;
    }
    
    private static function getRenewPrice($address, $num) {
        
        if ($num <= 0){
            return 0;
        }
        
        //海外300;
        $price =  isset(self::$renew[$address]) ? self::$renew[$address] : 150;
        return $price * $num;
    }

}
