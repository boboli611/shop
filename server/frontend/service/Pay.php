<?php
namespace frontend\service;
use \yii\db\Exception as Exception;


class Pay {

    public function storage($userId, $orderId, $price) {

        $order = \common\models\comm\CommOrder::findOne($orderId);
         
        if (!$order) {
            throw new Exception("订单不存在", ["order_id" => $orderId]);
        }
        
        if (!$order->status != \common\models\comm\CommOrder::status_add) {
            throw new Exception("订单已处理", ["order_id" => $orderId]);
        }

        if ($order->user_id != $userId) {
            throw new Exception("订单用户和扣款用户不是同一个人", ["order_user" => $order->user_id, "product_user" => $userId, "order_id" => $orderId]);
        }

        if ($order->pay_price < $price) {
            throw new Exception("付款不足", ["order_pay_price" => $order->pay_price, "price" => $price, "order_id" => $orderId]);
        }

        $product = \common\models\comm\CommProduct::findOne($order->product_id);
        if (!$product) {
            throw new Exception("商品不存在", ["product_id" => $order->product_id, "order_id" => $orderId]);
        }

        if ($product->count < $order->num) {
            throw new Exception("库存不足", ["order_num" => $order->num, "product_num" => $product->count, "order_id" => $orderId]);
        }

        $transaction = \common\models\comm\CommOrder::getDb()->beginTransaction();
        try {
            $order->status = \common\models\comm\CommOrder::status_pay_sucess;
            $product->count -= $order->num;
            $product->sell += $order->num;
            
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
