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
            return false;
        }
        
        $order = \common\models\comm\CommOrder::find()->where(["order_id" => $id])->one();
        if (!$order){
            $this->asJson(widgets\Response::error("单号错误"));
            return false;
        }
                echo "aaa";exit;
        $uid = widgets\User::getUid();
        if ($order->user_id != $uid){
            $this->asJson(widgets\Response::error("不是自己订单"));
            return false;
        }
        
        $orderProduct = \common\models\comm\CommOrderProduct::find()->where(["order_id" => $id])->one();
        if (!$orderProduct){
            $this->asJson(widgets\Response::error("订单商品错误"));
            return false;
        }
        
        $product = \common\models\comm\CommProduct::findOne($orderProduct->product_id);
        if (!$product){
            $this->asJson(widgets\Response::error("商品不存在"));
            return false;
        }
        
        $info = \common\models\comm\CommExpressLog::find()->where(['no' => $order->expressage])->one();
        if (!$info){
            $this->asJson(widgets\Response::error("数据为空"));
            return false;
        }
        
        $info['content'] = json_decode($info['content'], TRUE);
        
        $this->asJson($info);
    }
}