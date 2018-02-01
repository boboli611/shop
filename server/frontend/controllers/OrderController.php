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
    
    public function actionList(){
        
        $page = (int) Yii::$app->request->get("p");
        $type = (int) Yii::$app->request->get("type");
        if (!$type) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $list = \common\models\comm\CommOrder::getByUser($uid, $type, $page);
        $out = $pids = $products = [];
        
        foreach ($list as $val){
            $id = $val['product_id'];
            $pids[$id] = 1;
        }
        
        $ids = array_keys($pids);
        
        $pList = \frontend\service\Product::getByStorageid($ids);
        
        foreach ($pList as $val){
            $id = $val['storage_id'];
            $products[$id] = $val;
        }

        foreach ($list as $val){
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
        $this->asJson(widgets\Response::sucess($out));
    }
}