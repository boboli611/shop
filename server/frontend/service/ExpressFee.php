<?php
namespace frontend\service;
class ExpressFee {

    //首重
    static $first_price = [];
    static $renew = [];

    public static function sumPrice($address, $num) {
        
        $price = self::getFirstPrice($address) + self::getRenewPrice($address, $num - 1);
        return $price;
    }

    private static function getFirstPrice($address) {
        
        if(!self::$first_price){
            self::init();
        }
        
        //海外300;
        return isset(self::$first_price[$address]) ? self::$first_price[$address] : 30000;
    }
    
    private static function getRenewPrice($address, $num) {
        
        if ($num <= 0){
            return 0;
        }
        
        if(!self::$first_price){
            self::init();
        }
        
        //海外;
        $price =  isset(self::$renew[$address]) ? self::$renew[$address] : 15000;
        return $price * $num;
    }
    
    private static function init(){
        
        $list = \common\models\comm\CommRegion::find()->all();
        foreach ($list as $val){
            self::$first_price[$val->name] = $val->price;
            self::$renew[$val->name] = $val->renew;
        }
    }

}
