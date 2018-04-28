<?php
namespace frontend\service;
use \yii\db\Exception as Exception;

class Product {
    
    private static $orderField = [1 => "sell", 2 => "id"];
    private static $order = [1 => "desc", 2 => "asc"];
    
    public static function search($condiction, $key, $orderField, $orderType, $page = 0){
        
       $condiction = !is_array($condiction) ? [] : $condiction;
       $orderField = isset(self::$orderField[$orderField]) ? self::$orderField[$orderField] : self::$orderField[1];
       $orderType = isset(self::$order[$orderType]) ? self::$order[$orderType] : self::$order[1];
       $order = "{$orderField} {$orderType}";
       $page = (int) $page;
       
       $out = \common\models\comm\CommProduct::getList([], $key, $order, $page);
       
       return $out;
    }
}
