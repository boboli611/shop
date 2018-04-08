<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use common\widgets;
use \yii\db\Exception as Exception;

/**
 * Site controller
 */
class PayController extends Controller {

    public function actionShopPay() {

        $ids = Yii::$app->request->post("id");
        $addressId = Yii::$app->request->post("address_id");
        $content = Yii::$app->request->post("content");
        $ticketId = Yii::$app->request->post("ticket_id");
//        $addressId = 23;
        $ids = "32, 27";
        $ids = explode(',', $ids);

        if (!$ids) {
            $this->asJson(widgets\Response::error("商品不为空"));
            return;
        }

        if (!$addressId) {
            $this->asJson(widgets\Response::error("请选择地址"));
            return;
        }

        $pIds = [];
        $uid = widgets\User::getUid();
        $shopList = \common\models\user\UserShop::find()->where(['user_id' => $uid])->andWhere(['in', "id", $ids])->all();
        if (!$shopList) {
            $this->asJson(widgets\Response::error("商品数据错误"));
            return;
        }

        foreach ($shopList as $val) {
            $pIds[$val->storage_id] = $val->num;
        }

        try {
            $order = (new \frontend\service\Pay())->add($uid, $pIds, $addressId, $ticketId, $content);
            //\common\models\user\UserShop::deleteAll(['in', "id", $ids]);
        } catch (Exception $ex) {
            $this->asJson(widgets\Response::error($ex->getMessage()));
            return;
        }

        $out['nonceStr'] = $order['nonce_str'];
        $out['package'] = "prepay_id={$order['prepay_id']}";
        $out['sign'] = $order['paySign'];
        $out["timeStamp"] = (string) time();
        $this->asJson(widgets\Response::sucess($out));
    }

    public function actionOrder() {

        $order_id = Yii::$app->request->get("order_id");
        if (!$order_id) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $uid = widgets\User::getUid();
        $order = \common\models\comm\CommOrder::find()->where(['order_id' => $order_id])->one();
        if (!$order || $order->user_id != $uid) {
            $this->asJson(widgets\Response::error("非法参数"));
            return;
        }

        if ($order->status != \common\models\comm\CommOrder::status_waiting_pay) {
            $this->asJson(widgets\Response::error("不是未支付订单"));
            return;
        }

        $openid = \common\models\user\UserWxSession::findOne($uid);
        if (!$openid) {
            $this->asJson(widgets\Response::error("未登录"));
            return;
        }

        $storageList = \common\models\comm\CommOrderProduct::find()->where(['order_id' => $order_id])->all();
        foreach ($storageList as $val) {
            $product = \common\models\comm\CommProductionStorage::findOne($val->product_id);
            if ($product->num <= 0) {
                $this->asJson(widgets\Response::error("已售罄"));
                return;
            }

            if ($val->num > $product->num) {
                $this->asJson(widgets\Response::error("库存不足"));
                return;
            }

            if (!$product->status) {
                $this->asJson(widgets\Response::error("已下架"));
                return;
            }
        }

        $product = (object) [];
        $product->order_id = $order->id;
        $product->price = $order->total;

        $order = \frontend\components\WxpayAPI\Pay::pay($openid['open_id'], $product);
        if (!$order['prepay_id'] || $order['return_code'] == "FAIL") {
            $this->asJson(widgets\Response::error("下单失败"));
            return;
        }

        $out['nonceStr'] = $order['nonce_str'];
        $out['package'] = "prepay_id={$order['prepay_id']}";
        $out['sign'] = $order['paySign'];
        $out["timeStamp"] = (string) time();
        $this->asJson(widgets\Response::sucess($out));
    }

}
