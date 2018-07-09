<?php

namespace frontend\service;

use \yii\db\Exception as Exception;

class Pay {

    public function add($uid, $pIds, $addressId, $ticketId, $content) {

        //$model = \common\models\comm\CommOrder::getDb()->beginTransaction();
        try {
            $openid = \common\models\user\UserWxSession::findOne($uid);
            if (!$openid) {
                throw new Exception("非法用户");
            }

            $orderId = \common\models\comm\CommOrder::createOrderId($uid);
            $address = $this->getAddressInfo($uid, $addressId);


            $pInfo = $this->getCountProduct($uid, $pIds, $address, $content, $orderId);

            //抵扣优惠券
            $ticketPrice = (new \frontend\service\Ticket())->subTicket($uid, $ticketId, $pInfo["price"], $orderId);
            //物流费
            $freight = $this->getfreightPrice($address['province'], $pInfo['countProduct']);
            $countPrice = $pInfo["price"] - $ticketPrice + $freight;
            $userInfo = \common\models\user\User::findOne($uid);
            $product = (object) [];
            $product->title = "Lipze:{{$userInfo->username}}";
            $product->order_id = $orderId;
            $product->price = $countPrice;
            
            $order = \common\components\WxpayAPI\Pay::pay($openid['open_id'], $product);
            if (!$order['prepay_id'] || $order['return_code'] == "FAIL") {
                throw new Exception("下单失败");
            }
      
            $orderModel = new \common\models\comm\CommOrder();
            $orderModel->user_id = $uid;
            $orderModel->order_id = $orderId;
            $orderModel->num = $pInfo["countProduct"];
            $orderModel->freight = $freight;
            $orderModel->ticket = $ticketPrice;
            $orderModel->content = $content;
            $orderModel->prepay_id = $order['prepay_id'];
            $orderModel->address = json_encode($address);
            $orderModel->total = $pInfo["price"];
            $orderModel->pay_time = date("Y-m-d H:i:s");
            $orderModel->status = \common\models\comm\CommOrder::status_waiting_pay;
            $orderModel->refund = \common\models\comm\CommOrder::status_refund_no;
            $orderModel->prepay_id = $order['prepay_id'];

            if (!$orderModel->save()) {
                throw new Exception("下单失败");
            }

            //$model->commit();
        } catch (Exception $ex) {
            //$model->rollBack();
            throw new Exception($ex->getMessage());
        }

        $order['order_id'] = $orderId;
        return $order;
    }

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

        $transaction = \common\models\comm\CommOrder::getDb()->beginTransaction();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if (!$order) {
            throw new Exception("订单不存在", ["order_id" => $orderId]);
        }

        if ($order->status != \common\models\comm\CommOrder::status_waiting_pay) {
            throw new Exception("订单已处理", ["order_id" => $orderId]);
        }

        if ($order->total > $price) {
            throw new Exception("付款不足", ["order_pay_price" => $order->pay_price, "price" => $price, "order_id" => $orderId]);
        }
        try {


            $productList = \common\models\comm\CommOrderProduct::find()->where(['order_id' => $orderId])->all();
            foreach ($productList as $val) {

                $product = \common\models\comm\CommProductionStorage::findOne($val->product_id);
                if (!$product) {
                    throw new Exception("商品不存在", ["product_id" => $order->product_id, "order_id" => $orderId]);
                }

                if ($product->num < $val->num) {
                    //throw new Exception("库存不足", ["order_num" => $order->num, "product_num" => $product->count, "order_id" => $orderId]);
                }
            }

            $order->status = \common\models\comm\CommOrder::status_goods_waiting_send;
            $product->num--;
            $product->sell++;
            
            $pro = \common\models\comm\CommProduct::findOne($product->product_id);
            $pro->sell++;
            if (!$order->save() || !$product->save()) {
                throw new Exception("保存失败", ["product_id" => $order->product_id, "order_id" => $orderId]);
            }

            $pro->save();
            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }

    private function getAddressInfo($uid, $addressId) {

        $addres = \common\models\user\UserAddress::findOne($addressId);
        if (!$addres || $addres->user_id != $uid) {
            throw new Exception("非法参数");
        }

        $addresData['region'] = sprintf("%s,%s,%s", $addres->province, $addres->city, $addres->county);
        $addresData['province'] = $addres->province;
        $addresData['city'] = $addres->city;
        $addresData['county'] = $addres->county;
        $addresData['address'] = $addres->address;
        $addresData['name'] = $addres->name;
        $addresData['user_id'] = $addres->user_id;
        $addresData['mobile'] = $addres->mobile;
        //$address = json_encode($addresData);

        return $addresData;
    }

    private function getCountProduct($uid, $pIds, $address, $content, $orderId) {

        $product = \common\models\comm\CommProductionStorage::getByids(array_keys($pIds));
        if (!$product || count($product) != count($pIds)) {
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }

        $countPrice = 0;
        $countProduct = 0;
        foreach ($product as $item) {

            $item->price = $item->price * 100;
            $num = $item->num;
            if ($num <= 0) {
                throw new Exception("已售罄");
            }

            if ($pIds[$item->id] > $num) {
                throw new Exception("库存不足");
            }

            if (!$item->status) {
                throw new Exception("已下架");
            }

            $model = new \common\models\comm\CommOrderProduct();
            $model->order_id = $orderId;
            $model->product_id = $item->id;
            $model->user_id = $uid;
            $model->num = $pIds[$item->id];
            $model->price = $item->price * $pIds[$item->id];
            $model->pay_price = $item->price;

            if (!$model->save()) {
                throw new Exception("下单失败");
            }

            $countProduct += (int) $pIds[$item->id];
            $countPrice += $model->price;
        }

        $out['price'] = $countPrice;
        $out['countProduct'] = $countProduct;
        return $out;
    }
    
    //运费
    public function getfreightPrice($address, $num){
        return ExpressFee::sumPrice($address, $num);
    }
}
