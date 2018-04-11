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
        $info = \common\models\comm\CommOrder::getInfoByOrder($id, $uid);
        $out = $pids = $products = [];
        
        if (!$info){
            $this->asJson(widgets\Response::sucess($out));
            return;
        }
        
        foreach ($info as $val) {
            $val = $val->toArray();
            $price = $val['total'];
            $order = $val['order_id'];
            $create_time = $val['created_at'];
            $id = $val['product_id'];
            $pids[] = $id;
            $address = $val['address'];
            $status = $val['status'];
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
            'total' => $price / 100,
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
        $list = \common\models\comm\CommOrder::getListInfo($uid, $type, $page);
        if (!$list){
            $this->asJson(widgets\Response::sucess([]));
            return;
        }

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
            $out[$id]['total'] = $val->total / 100;
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
        
        $orderId = Yii::$app->request->post("order_id");
        $recive = Yii::$app->request->post("recive");
        $content = Yii::$app->request->post("content");
        
        if (!$orderId){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
        
        if (!$recive){
            $this->asJson(widgets\Response::error("收货状态不能为空"));
            return;
        }
        
        if ($recive != CommOrder::status_goods_waiting_receve && $recive != CommOrder::status_goods_receve){
            $this->asJson(widgets\Response::error("收货状态错误"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能退换别人的货物"));
            return;
        }
        
        if ($order->refund != \common\models\comm\CommOrder::status_refund_no){
            $this->asJson(widgets\Response::error("已申请退货"));
            return;
        }
        
        $order->refund = \common\models\comm\CommOrder::status_refund_waiting;
        $data = [
                'refund' => \common\models\comm\CommOrder::status_refund_waiting,
                'content' => $content,
                'refund_status' => $recive,
                ];
        $ret = \common\models\comm\CommOrder::updateAll($data, "order_id='{$orderId}'");
        if (!$ret){
            $this->asJson(widgets\Response::error("申请失败"));
            return;
        }
        
        $order = $order->toArray();
        $order['order_status_text'] = \common\models\comm\CommOrder::$refund[\common\models\comm\CommOrder::status_refund_waiting];
        $this->asJson(widgets\Response::sucess($order));
    }
    
    //退货
    public function actionRetunExpressage(){
        
        $orderId = Yii::$app->request->get("order_id");
        $num = Yii::$app->request->get("num");
        if (!$orderId){
            $this->asJson(widgets\Response::error("单号不能为空"));
            return;
        }
        
        if (!$num){
            $this->asJson(widgets\Response::error("单号不能为空"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能退换别人的货物"));
            return;
        }
        
        if ($order->status != CommOrder::status_goods_receve){
            $this->asJson(widgets\Response::error("只有已收货需要填单号"));
            return;
        }
        
        if($order->refund != CommOrder::status_refund_ok){
            $this->asJson(widgets\Response::error("审批未通过"));
            return;
        }
        
        if ($order->return_expressage){
            $this->asJson(widgets\Response::error("已填写单号"));
            return;
        }
        
        $order->refund = \common\models\comm\CommOrder::status_refund_waiting;
        $ret = \common\models\comm\CommOrder::updateAll(['return_expressage' => $num], "order_id='{$orderId}'");
        if (!$ret){
            $this->asJson(widgets\Response::error("填写快递单失败"));
            return;
        }
        
        $order = $order->toArray();
        $order['order_status_text'] = \common\models\comm\CommOrder::$refund[\common\models\comm\CommOrder::status_refund_waiting];
        $this->asJson(widgets\Response::sucess());
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
