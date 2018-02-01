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
    
    public static function getByStorageid($storageId){
        
        if (!is_array($storageId)){
            return [];
        }
        
        $storageId = implode(',', $storageId);
        $sql = "select b.*, a.id as storage_id from comm_production_storage a "
                . " inner join comm_product b on a.product_id = b.id"
                . " where a.id in({$storageId}) and a.status = 1 and b.status = 1";
                
        $info = \common\models\comm\CommProduct::findBySql($sql)->asArray()->all();
        if ($info){
            return $info;
        }else{
            return [];
        }
    }
}
