<?php
namespace frontend\service;
use \yii\db\Exception as Exception;

class Product {
    
    private static $orderField = [1 => "sell", 2 => "id"];
    private static $order = [1 => "desc", 2 => "asc"];
    
    public static function search($condiction, $key , $orderField, $orderType, $page = 0, $limit = 10){
        
       $condiction = !is_array($condiction) ? [] : $condiction;
       $orderField = isset(self::$orderField[$orderField]) ? self::$orderField[$orderField] : self::$orderField[1];
       $orderType = isset(self::$order[$orderType]) ? self::$order[$orderType] : self::$order[1];
       $order = "{$orderField} {$orderType}";
       $page = (int) $page >0 ?$page - 1 : 0;
       
       $out = \common\models\comm\CommProduct::getList([], $key, $order, $page,$limit);
       
       return $out;
    }
    
    /**
     * 库存id查询商品信息
     * @param type $storageId
     * @return type
     */
    public static function getByStorageid($storageId){
        
        if (!is_array($storageId) || !$storageId){
            return [];
        }
        
        $storageId = implode(',', $storageId);
        $sql = "select b.*,b.id as pid, a.id as storage_id, a.price as storage_price,a.style,a.size from comm_production_storage a "
                . " inner join comm_product b on a.product_id = b.id"
                . " where a.id in({$storageId}) and a.status = 1 and b.status = 1";
                
        $info = \common\models\comm\CommProduct::findBySql($sql)->asArray()->all();
        if ($info){
            return $info;
        }else{
            return [];
        }
    }
    
    /**
     * 推荐商品
     */
    public static function getRecommond(){
        
        $recommends = \common\models\comm\CommProductRecommend::find()->orderBy("id desc")->all(); 
        $ids = [];
        foreach ($recommends as $val){
            $ids[] = $val->product_id;
        }
        if (!$ids){
            return [];
        }
        return self::search(["type" => 1], "", "", "");
    }
}
