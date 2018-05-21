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
class ExpressController extends Controller {
    
    public function actionGet(){

        $id =  Yii::$app->request->get("id");
        if (!$id){
            $this->asJson(widgets\Response::error("单号不能为空"));
            return;
        }
        
        $order = \common\models\comm\CommOrder::find()->where(["order_id" => $id])->one();
        if (!$order){
            $this->asJson(widgets\Response::error("单号错误"));
            return;
        }
        $uid = widgets\User::getUid();
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不是自己订单"));
            return;
        }
        
        $orderProduct = \common\models\comm\CommOrderProduct::find()->where(["order_id" => $id])->one();
        if (!$orderProduct){
            $this->asJson(widgets\Response::error("订单商品错误"));
            return;
        }
        $product = \common\models\comm\CommProductionStorage::getInfoById($orderProduct->product_id);
        if (!$product){
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }
        
        $info = \common\models\comm\CommExpressLog::find()->where(['no' => $order->expressage])->one();
        if (!$info){
            $this->asJson(widgets\Response::error("数据为空"));
            return;
        }

        $info = $info->toArray();
        $product['cover'] = json_decode($product['cover'], TRUE);
        $info['content'] = json_decode($info['content'], TRUE);
        foreach ($info['content'] as $k => $v){
            $info['content'][$k]['date'] = substr($v['time'], 5, 5);
            $info['content'][$k]['hour'] = substr($v['time'], 11, 5);
        }

        $out['product']['status'] = $product['status'];
        $out['product']['cover'] = $product['cover'][0];
        $out['product']['address'] = json_decode($order->address, true);
        $out['express']['content'] = $info['content'];
        $out['express']['company_name'] = \common\models\comm\CommExpressLog::$company[$info['company']];
        $out['express']['no'] = $info['no'];
        $this->asJson(widgets\Response::sucess($out));
    }
}