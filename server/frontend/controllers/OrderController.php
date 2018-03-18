<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\widgets;
use common\models\comm\CommOrder;

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
            $address = $val->address;
            $status = $val->status;
        }
     
        $pList = \frontend\service\Product::getByStorageid($pids);
        foreach ($pList as &$val) {
            $val['cover'] = json_decode($val['cover'], true);
            $val['cover'] = $val['cover'][0];
            $id = $val['storage_id'];
            $val['num'] = 1;
            $out['goods'][$id] = $val;
            $out['goods'][$id]['pay_price'] = $val['storage_price'] / 100;
        }

        $address = json_decode($address);

        $out['info'] = ['order_id' => $order, 
            'price' => $price / 100,
            'status'=> (int)$status,
            'created_at' => $create_time,
            'order_status_text' =>  \common\models\comm\CommOrder::$payName[$status],
            'consignee' => $address->name, 'mobile' => $address->mobile,
            'address' => $address->full_region .' '. $address->address,
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
        
        $list = \common\models\comm\CommOrder::find()->where(['in', 'order_id', $ids])->orderBy("id desc")->all();
        foreach ($list as $val) {
            $id = $val['product_id'];
            $pids[$id] = 1;
        }

        $ids = array_keys($pids);

        $pList = \frontend\service\Product::getByStorageid($ids);

        foreach ($pList as &$val) {
            $id = $val['storage_id'];
            $val['cover'] = json_decode($val['cover'], true);
            $val['cover'] = $val['cover'][0];
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
            $out[$id]['order_status_text'] = $val->refund == CommOrder::status_refund_no ? CommOrder::$payName[$status] : CommOrder::$refund[$val->refund];
            $out[$id]['goodsList'][] = $product;
        }

        //$out['data'] =  $list;
        $this->asJson(widgets\Response::sucess(array_values($out)));
    }

    //退货
    public function actionRefund(){
        
        $orderId = Yii::$app->request->get("order_id");
        if (!$orderId){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        //$uid = 7;
        $uid = widgets\User::getUid();
        
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
            $this->asJson(widgets\Response::error("已申请退货"));
            return;
        }
        
        $order->refund = \common\models\comm\CommOrder::status_refund_waiting;
        $ret = \common\models\comm\CommOrder::updateAll(['refund' => \common\models\comm\CommOrder::status_refund_waiting], "order_id='{$orderId}'");
        if (!$ret){
            $this->asJson(widgets\Response::error("申请失败"));
            return;
        }
        
        $order = $order->toArray();
        $order['order_status_text'] = \common\models\comm\CommOrder::$refund[\common\models\comm\CommOrder::status_refund_waiting];
        $this->asJson(widgets\Response::sucess($order));
    }
    
    //确认收货
    public function actionReceve(){
        
        $orderId = Yii::$app->request->get("order_id");
        if (!$orderId){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $uid = widgets\User::getUid();
        
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
            $this->asJson(widgets\Response::error("已申请退货"));
            return;
        }
        
        $ret = CommOrder::updateAll(['status' => CommOrder::status_goods_receve], "order_id='{$orderId}'");
        if (!$ret){
            $this->asJson(widgets\Response::error("确认失败"));
            return;
        }
        
        $order = $order->toArray();
        $order['order_status_text'] = CommOrder::$payName[CommOrder::status_goods_receve];
        $this->asJson(widgets\Response::sucess($order));
    }
}
