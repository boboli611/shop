<?php
namespace frontend\service;
use \yii\db\Exception as Exception;


class Pay {

    /**
     * 订单入库
     * @param type $userId
     * @param type $orderId
     * @param type $price
     * @return boolean
     * @throws Exception
     * @throws \Exception
     */
    public function storage($orderId, $price) {

        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if (!$order) {
            throw new Exception("订单不存在", ["order_id" => $orderId]);
        }
        
        if ($order->status != \common\models\comm\CommOrder::status_waiting_pay) {
            throw new Exception("订单已处理", ["order_id" => $orderId]);
        }

        if ($order->pay_price < $price) {
            throw new Exception("付款不足", ["order_pay_price" => $order->pay_price, "price" => $price, "order_id" => $orderId]);
        }

        $product = \common\models\comm\CommProductionStorage::findOne($order->product_id);
        if (!$product) {
            throw new Exception("商品不存在", ["product_id" => $order->product_id, "order_id" => $orderId]);
        }

        if ($product->num < $order->num) {
            throw new Exception("库存不足", ["order_num" => $order->num, "product_num" => $product->count, "order_id" => $orderId]);
        }

        $transaction = \common\models\comm\CommOrder::getDb()->beginTransaction();
        try {
            $order->status = \common\models\comm\CommOrder::status_goods_waiting_send;
            $product->num--;
            $product->sell++;
            
            $order->save();
            $product->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } 
        
        return true;
    }

}
