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
        if (!$id){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
        $uid = widgets\User::getUid();
        $info = \common\models\comm\CommOrder::getInfoByOrder($id, $uid);
        $out = $pids = $products = [];
        
        if (!$info){
            $this->asJson(widgets\Response::sucess($out));
            return;
        }

        $orderProduct = [];
        foreach ($info as $val) {
            $price = $val['total'];
            $freight = (int)$val['freight'];
            $order = $val['order_id'];
            $create_time = $val['created_at'];
            $id = $val['product_id'];
            $pids[] = $id; 
            $orderProduct[$id]['refound_status'] = (int)$val['refound_status'];
            $orderProduct[$id]['refound_id'] = (int)$val['refound_id'];
            $orderProduct[$id]['num'] = (int)$val['num'];
            $address = $val['address'];
            $status = $val['status'];
            $content = $val['content'];
            $pay_time = $val['pay_time'];
            $send_time = $val['send_time'];
            $end_time = $val['end_time'];
            $expressage = $val['expressage'];
        }
        $pList = \frontend\service\Product::getByStorageid($pids);
        foreach ($pList as $key => &$val) {
            $id = $val['storage_id'];
            $val = array_merge($val, is_array($orderProduct[$id]) ? $orderProduct[$id] : []);
            //var_dump($id, $orderProduct[$id]);
            $val['cover'] = json_decode($val['cover'], true);
            $val['cover'] = $val['cover'][0];
            
           
            $out['goods'][$id] = $val;
            $out['goods'][$id]['pay_price'] = $val['storage_price'] / 100;
            
        }

        $address = json_decode($address);

        $out['info'] = ['order_id' => $order, 
            'total' => $price / 100,
            'freight' => $freight / 100,
            'status'=> (int)$status,
            'content'=> $content,
            'created_at' => $create_time,
            'order_status_text' =>  \common\models\comm\CommOrder::$payName[$status],
            'consignee' => $address->name, 'mobile' => $address->mobile,
            'address' => $address->region .' '. $address->address,
            'pay_time' => $pay_time,
            'send_time' => $send_time,
            'end_time' => $end_time,
            'expressage' =>$expressage,
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

        $products = [];
        foreach ($pList as &$val) {
            $id = $val['storage_id'];
            $val['cover'] = json_decode($val['cover'], true);
            $val['cover'] = $val['cover'][0];
            $products[$id] = $val;
        }

        $out = [];
        foreach ($list as $val) {
            $id = $val->order_id;
            $sid = $val->product_id;
            $status = $val->status;
            if (!$products[$sid]){
                continue;
            }
            $product = $products[$sid];
            $product['num'] = $val->num;
            $product['price'] = $product['price']/ 100;
            $out[$id]['expressage'] = $val->expressage;
            $out[$id]['total'] = ($val->total + (int)$val->freight) / 100;
            $out[$id]['num'] += $val->num;
            $out[$id]['order_id'] = $id;
            $out[$id]['status'] = $status;
            $out[$id]['order_status_text'] = $val->refund == CommOrder::status_refund_no ? CommOrder::$payName[$status] : CommOrder::$refund[$val->refund];
            $out[$id]['goodsList'][] = $product;
        }

        //$out['data'] =  $list;
        $this->asJson(widgets\Response::sucess(array_values($out)));
    }
    
    //退货详情
    public function actionRefundApply(){
        
        $orderId = Yii::$app->request->post("id");
        $storageId = Yii::$app->request->post("storage_id");
        if (!$orderId){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
        
        if (!$storageId){
            $this->asJson(widgets\Response::error("商品ID不为空"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能退换别人的货物"));
            return;
        }
        
        $refund = \common\models\comm\CommOrderRefundLog::find()->where(['=','order_id', $orderId])->andWhere(['in', 'storage_id' , $storageId])->one();
        if ($refund){
            $this->asJson(widgets\Response::error("已申请退货"));
            return;
        }
        
        $product = $pList = \frontend\service\Product::getByStorageid([$storageId]);
        if (!$product){
            $this->asJson(widgets\Response::error("选择商品错误"));
            return;
        }
        
        $orderProduct = \common\models\comm\CommOrderProduct::find()->where(['order_id' => $orderId])->andWhere(['product_id' => $storageId])->one();
        if (!$orderProduct){
            $this->asJson(widgets\Response::error("选择商品错误"));
            return;
        }
        
        $product = $product[0];
        $cover = json_decode($product['cover'], true);
        $product['cover'] = $cover[0];
        $product['price'] = $orderProduct['pay_price'] / 100;
        $product['num'] = $orderProduct['num'];
        $out['product'] = $product;
        $out['order']['totle'] = $orderProduct['pay_price'] * $orderProduct['num']/ 100;
        $out['order']['status'] = $order->status;
        $this->asJson(widgets\Response::sucess($out));
    }
    
    
    //退货详情
    public function actionRefundDetail(){
        
        $id = Yii::$app->request->post("id");
        if (!$id){
            $this->asJson(widgets\Response::error("参数不能为空"));
            return;
        }
       
        $refund = \common\models\comm\CommOrderRefundLog::findOne($id);
        if (!$refund){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $orderProduct = \common\models\comm\CommOrderProduct::find()->where(['order_id' => $refund->order_id])->one();
        if (!$orderProduct){
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }
        
        $uid = widgets\User::getUid();
        if ($orderProduct->user_id != $uid){
            $this->asJson(widgets\Response::error("不是自己的订单"));
            return;
        }
        
        $product = $pList = \frontend\service\Product::getByStorageid([$orderProduct->product_id]);
        if (!$product){
            $this->asJson(widgets\Response::error("选择商品错误"));
            return;
        }

        
        $product = $product[0];
        $cover = json_decode($product['cover'], true);
        $product['cover'] = $cover[0];
        $product['price'] = $orderProduct['pay_price'] / 100;
        $product['num'] = $orderProduct['num'];
        $out['product'] = $product;
        $out['order']['total'] = $orderProduct['pay_price'] * $orderProduct['num']/ 100;
        $out['order']['content'] = $refund->content;
        $out['order']['expressage_status'] = $refund->expressage_status;
        $out['order']['expressage_num'] = $refund->expressage_num;
        $out['order']['order_id'] = $refund->order_id;
        $this->asJson(widgets\Response::sucess($out));
    }
    
    //上传单号
    public function actionUploadExpressage(){
        
        $id = Yii::$app->request->post("id");
        $expressage = Yii::$app->request->post("expressage");
        $mobile = Yii::$app->request->post("mobile");
        $expre_company = Yii::$app->request->post("expre_company");
        if (!$id){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        if (!$expressage){
            $this->asJson(widgets\Response::error("输入快递单号"));
            return;
        }
        
        if (!$expre_company){
            $this->asJson(widgets\Response::error("请选择快递公司"));
            return;
        }
        
        if (!$mobile){
            $this->asJson(widgets\Response::error("输入电话号码"));
            return;
        }
        
        $refund = \common\models\comm\CommOrderRefundLog::findOne($id);
        if (!$refund){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $orderProduct = \common\models\comm\CommOrderProduct::find()->where(['order_id' => $refund->order_id])->one();
        if (!$orderProduct){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        if ($refund->expressage_num){
            $this->asJson(widgets\Response::error("已上传快递单号"));
            return;
        }
        
        $uid = widgets\User::getUid();
        if ($orderProduct->user_id != $uid){
            $this->asJson(widgets\Response::error("不是自己的订单"));
            return;
        }
        
        $refund->expressage_status = CommOrder::status_goods_receve;
        $refund->expressage_num = $expressage;
        $refund->expre_company = $expre_company;
        $refund->mobile = $mobile;
        if (!$refund->save()){
            $this->asJson(widgets\Response::error("提交失败"));
            return;
        }
        
        $this->asJson(widgets\Response::sucess([]));
    }

    //退货
    public function actionRefund(){
        
        $orderId = Yii::$app->request->post("order_id");
        $storageId = Yii::$app->request->post("storage_id");
        $recive = Yii::$app->request->post("recive");
        $content = Yii::$app->request->post("content");
        $storageId = explode(',', $storageId);
        if (!$orderId){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
        
        if (!$storageId || !is_array($storageId)){
            $this->asJson(widgets\Response::error("商品ID不为空"));
            return;
        }
        
        
        
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能退换别人的货物"));
            return;
        }
        
        if ($order->status == CommOrder::status_goods_waiting_send){
            $recive = CommOrder::status_goods_waiting_send;
        }else if ($recive != CommOrder::status_goods_waiting_receve && $recive != CommOrder::status_goods_receve){
            $this->asJson(widgets\Response::error("收货状态错误"));
            return;
        }
        
        $refund = \common\models\comm\CommOrderRefundLog::find()->where(['=','order_id', $orderId])->andWhere(['in', 'storage_id' , $storageId])->one();
        if ($refund){
            $this->asJson(widgets\Response::error("已申请退货"));
            return;
        }
        
        $trans = \common\models\comm\CommOrderRefundLog::getDb()->beginTransaction();
        try {
            
            foreach ($storageId as $id){
                $model = new \common\models\comm\CommOrderRefundLog();
                $storage  = \common\models\comm\CommOrderProduct::find()->where(['=','order_id', $orderId])->andWhere(['product_id' => $id])->one();
                if (!$storage){
                    throw new \Exception("商品未购买");
                }
                $model->order_id = $orderId;
                $model->storage_id = $id;
                $model->content = $content;
                $model->refound = CommOrder::status_refund_checking;
                $model->expressage_status = $recive;
                $model->price = $storage->pay_price;
                if (!$model->save()){
                    throw new \Exception("申请失败");
                }
            }
            $trans->commit();
        } catch (\Exception $exc) {
            $trans->rollBack();
            $this->asJson(widgets\Response::error($exc->getMessage()));
            return;
        }


/*
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
        
 * 
 */       
        $order = [];
        $order['refund_id'] = $model->getPrimaryKey();
        $order['order_status_text'] = \common\models\comm\CommOrder::$refund[\common\models\comm\CommOrder::status_refund_waiting];
        $this->asJson(widgets\Response::sucess($order));
    }
    
    //取消退款
    public function actionCancelRefund(){
        
        $orderId = Yii::$app->request->post("order_id");
        $storageId = Yii::$app->request->post("storage_id");

        if (!$orderId){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
       
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能取消别人的申请"));
            return;
        }
        
       $refundLogModel =  \common\models\comm\CommOrderRefundLog::find()->where(['order_id' => $orderId])->andWhere(['storage_id' => $storageId])->one();
       if (!$refundLogModel){
            $this->asJson(widgets\Response::error("未申请退款"));
            return;
        }
        
        if(!$refundLogModel->delete()){
            $this->asJson(widgets\Response::error("取消失败"));
            return;
        }
        
        $this->asJson(widgets\Response::sucess([]));
    }
    
    
    //删除订单
    public function actionDelete(){
        
        $orderId = Yii::$app->request->post("orderId");
        if (!$orderId){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不能删除别人的订单"));
            return;
        }
        
        if ($order->status != CommOrder::status_goods_close){
            $this->asJson(widgets\Response::error("未关闭的订单不能删除"));
            return;
        }
        
        $trans = \common\models\comm\CommOrderRefundLog::getDb()->beginTransaction();
        try {
            
            $orderData = $order->toArray();
            $orderLog = new \common\models\comm\CommOrderDeleteLog();
            $orderLog->load(['CommOrderDeleteLog' => $orderData]);
            if(!$orderLog->save()){
                throw new \Exception("操作失败");
            }
            
            $order->delete();
            $trans->commit();
        } catch (\Exception $exc) {
            $trans->rollBack();
            $this->asJson(widgets\Response::error($exc->getMessage()));
            return;
        }
    
        $this->asJson(widgets\Response::sucess([]));
    }
    
    //提醒发货
    public function actionNotice(){
        
        $orderId = Yii::$app->request->post("orderId");
        if (!$orderId){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不是自己的订单"));
            return;
        }
        
        if ($order->status == CommOrder::status_waiting_pay){
            $this->asJson(widgets\Response::error("请付款先"));
            return;
        }
        
        if ($order->status > CommOrder::status_goods_waiting_send){
            $this->asJson(widgets\Response::error("已发货"));
            return;
        }
        
        if ($order->notice){
            $this->asJson(widgets\Response::error("已提醒"));
            return;
        }
        
        try {
            
            $order->notice = 1;
            if (!$order->save()){
                $this->asJson(widgets\Response::error("操作失败"));
                return;
            }

        } catch (\Exception $exc) {
    
            $this->asJson(widgets\Response::error($exc->getMessage()));
            return;
        }
    
        $this->asJson(widgets\Response::sucess([]));
    }
    
    
     //关闭订单
    public function actionClose(){
        
        $orderId = Yii::$app->request->post("orderId");
        if (!$orderId){
            $this->asJson(widgets\Response::error("订单不能为空"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::getByOrderId($orderId);
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不是自己的订单"));
            return;
        }
        
        if ($order->status == CommOrder::status_waiting_pay){
            $this->asJson(widgets\Response::error("请付款先"));
            return;
        }
        
        if ($order->status == CommOrder::status_goods_waiting_send){
            $this->asJson(widgets\Response::error("还未发货"));
            return;
        }
        
        try {
            
            $order->status = CommOrder::status_goods_close;
            if (!$order->save()){
                $this->asJson(widgets\Response::error("操作失败"));
                return;
            }

        } catch (\Exception $exc) {
    
            $this->asJson(widgets\Response::error($exc->getMessage()));
            return;
        }
    
        $this->asJson(widgets\Response::sucess([]));
    }
    
    
    //退单快递号
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
