<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\widgets;

/**
 * index controller
 */
class OrderController extends Controller {

    public function actionDetail() {

        $id = Yii::$app->request->get("orderId");
        $uid = widgets\User::getUid();
        $info = \common\models\comm\CommOrder::find()->where(['order_id' => $id])->andWhere(['user_id' => $uid])->all();
        $out = $pids = $products = [];
        foreach ($info as $val) {
            $price += $val->price;
            $order = $val->order_id;
            $create_time = $val->created_at;
            $id = $val->product_id;
            $pids[] = $id;
            $address_id = $val->address;
        }


        $pList = \frontend\service\Product::getByStorageid($pids);
        foreach ($pList as $val) {

            $status = $val['status'];
            $pid = $val['pid'];
            $val['num'] = 1;
            $out['goods'][$pid] = $val;
            $out['goods'][$pid]['pay_price'] = $val['pay_price'] / 100;
            $out['goods'][$pid]['order_status_text'] = \common\models\comm\CommOrder::$payName[$status];
        }

        $address = \common\models\user\UserAddress::findOne($address_id);

        $out['info'] = ['order_id' => $order, 'price' => $price / 100,
            'create_at' => $create_time, 'order_status_text' => '已下单',
            'consignee' => $address->name, 'mobile' => $address->mobile,
            'address' => $address->full_region . $address->address,
        ];
        $this->asJson(widgets\Response::sucess($out));
    }

    public function actionList() {

        $page = (int) Yii::$app->request->get("p");
        $type = (int) Yii::$app->request->get("type");

        $uid = widgets\User::getUid();
        $list = \common\models\comm\CommOrder::getByUser($uid, $type, $page);
        $out = $ids = $pids = $products = [];
        foreach ($list as $val) {
            $id = $val['order_id'];
            $ids[] = $id;
        }
        
        $list = \common\models\comm\CommOrder::find()->where(['in', 'order_id', $ids])->all();
 
        foreach ($list as $val) {
            $id = $val['product_id'];
            $pids[$id] = 1;
        }

        $ids = array_keys($pids);

        $pList = \frontend\service\Product::getByStorageid($ids);

        foreach ($pList as $val) {
            $id = $val['storage_id'];
            $products[$id] = $val;
        }

        foreach ($list as $val) {
            $id = $val->order_id;
            $sid = $val->product_id;
            $status = $val->status;
            $product = $products[$sid];
            $product['num'] = $val->num;
            $out[$id]['pay_price'] = $val->pay_price / 100;
            $out[$id]['order_id'] = $id;
            $out[$id]['status'] = $status;
            $out[$id]['order_status_text'] = \common\models\comm\CommOrder::$payName[$status];
            $out[$id]['goodsList'][] = $product;
        }

        //$out['data'] =  $list;
        $this->asJson(widgets\Response::sucess(array_values($out)));
    }

    //退货
    public function actionRefund(){
        
        $orderId = Yii::$app->request->get("order_id");
        $orderId = '20180219151902206975662';
        if (!$orderId){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $uid = 7;
        //$uid = widgets\User::getUid();
        
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能退换别人的货物"));
            return;
        }
        
        if ($order->status == \common\models\comm\CommOrder::status_goods_receve){
            $this->asJson(widgets\Response::error("已签收"));
            return;
        }
        
        if ($order->refund != \common\models\comm\CommOrder::status_refund_no){
            $this->asJson(widgets\Response::error("已申请退换"));
            return;
        }
        
        $order->refund = \common\models\comm\CommOrder::status_refund_waiting;
        if (!$order->save()){
            $this->asJson(widgets\Response::error("申请失败"));
            return;
        }
        
        $this->asJson(widgets\Response::sucess($order));
    }
    
    //确认收货
    public function actionReceve(){
        
        $orderId = Yii::$app->request->get("order_id");
        $orderId = '20180219151902206975662';
        if (!$orderId){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $uid = 7;
        //$uid = widgets\User::getUid();
        
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能退换别人的货物"));
            return;
        }
        
         if ($order->status < \common\models\comm\CommOrder::status_goods_waiting_receve){
            $this->asJson(widgets\Response::error("未发货"));
            return;
        }
        
        if ($order->status == \common\models\comm\CommOrder::status_goods_receve){
            $this->asJson(widgets\Response::error("已签收"));
            return;
        }
        
        if ($order->refund != \common\models\comm\CommOrder::status_refund_no){
            $this->asJson(widgets\Response::error("已申请退换"));
            return;
        }
        
        $order->status = \common\models\comm\CommOrder::status_goods_receve;
        if (!$order->save()){
            $this->asJson(widgets\Response::error("确认失败"));
            return;
        }
        
        $this->asJson(widgets\Response::sucess($order));
    }
}
