<?php
namespace frontend\service;
use \yii\db\Exception as Exception;

class Product {
    
    private static $orderField = [1 => "sell", 2 => "sort"];
    private static $order = [1 => "desc", 2 => "asc"];
    
    public static function search($condiction, $key, $orderField, $orderType, $page = 0, $limit = 10){
        
       $condiction = !is_array($condiction) ? [] : $condiction;
       $orderField = isset(self::$orderField[$orderField]) ? self::$orderField[$orderField] : self::$orderField[2];
       $orderType = isset(self::$order[$orderType]) ? self::$order[$orderType] : self::$order[2];
       $order = "{$orderField} {$orderType}, id desc";
       $page = (int) $page;
       
       $out = \common\models\comm\CommProduct::getList($condiction, $key, $order, $page, $limit);
       
       return $out;
    }
}
