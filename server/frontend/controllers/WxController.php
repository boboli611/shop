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
class WxController extends Controller {

    public function actionLogin() {

        $appid = \yii::$app->params["wx"]['appId'];
        $secret = \yii::$app->params["wx"]['appSecret'];

        $code = $_SERVER['HTTP_X_WX_CODE'];
        $encryptedData = $_SERVER["HTTP_X_WX_ENCRYPTED_DATA"];
        $iv = $_SERVER["HTTP_X_WX_IV"];
     
        if (!$code || !$encryptedData || !$iv) {
            $this->asJson(widgets\Response::error("登录失败1"));
            return;
        }

        $grant_type = "authorization_code";
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=$grant_type";
        $result = widgets\Http::Get($url);
        $result = json_decode($result, true);

        if (isset($result['errcode'])) {
            $this->asJson(widgets\Response::error("登录失败2"));
            return;
        }

        $pc = new \frontend\components\WxpayAPI\WXBizDataCrypt($appid, $result['session_key']);
        $errCode = $pc->decryptData($encryptedData, $iv, $data);
        $data = json_decode($data, true);
        if ($errCode != 0 || !$data) {
            $this->asJson(widgets\Response::error("登录失败"));
            return;
        }

        try {
            $model = \common\models\user\UserWxSession::getByOpendId($data["openId"]);
            if (!$model) {
                $model = new \common\models\user\UserWxSession();
            }

            $transaction = \common\models\user\UserWxSession::getDb()->beginTransaction();
            $model->open_id = $data["openId"];
            $model->token = md5($data["openId"] . time() . rand(1, 1000));
            $model->unionId = $data["unionId"];
            $model->session_key = $result["session_key"];
            $model->expires_in = time() + 3600 * 24 * 10;
            $model->save();

            $userModel = \common\models\user\User::findOne($model->user_id);
            if (!$userModel) {
                $userModel = new \common\models\user\User();
            }

            $userModel->id = $model->user_id;
            $userModel->username = $data['nickName'];
            $userModel->gender = $data['gender'];
            $userModel->city = $data['city'];
            $userModel->province = $data['province'];
            $userModel->country = $data['country'];
            $userModel->img = $data['avatarUrl'];

            $userModel->login_at = date("Y-m-d H:i:s");
            $ret = $userModel->save();

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        $userInfo['username'] = $userModel->username;
        $userInfo["img"] = $userModel->img;
        $userInfo["token"] = $model->token;
        //$out["session_key"] = $result["session_key"];
        $out["userInfo"] = $userInfo;
        $out["token"] = $model->token;

        $this->asJson(widgets\Response::sucess($out));
    }

    //创建订单
    public function actionCreateOrder() {

        $data = json_decode($GLOBALS['HTTP_RAW_POST_DATA'], true);
        
        
        $pids = Yii::$app->request->post("ids");
        $addressId = Yii::$app->request->post("address_id");
        $pids = [$pids];
        
        if (!$pids || !$addressId) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        $uid = widgets\User::getUid();
        $addres = \common\models\user\UserAddress::findOne($addressId);
        if (!$addres || $addres->user_id != $uid) {
            $this->asJson(widgets\Response::error("非法参数"));
        }

        $addres = $addres->full_region + $addres->address;
        $openid = \common\models\user\UserWxSession::findOne($uid);
        
        if (!$openid) {
            $this->asJson(widgets\Response::error("未登录"));
        }

        $shop = [];
        foreach ($pids as $pid) {
            $shop[$pid] ++;
        }

        
        $product = \common\models\comm\CommProductionStorage::getByids(array_keys($shop));
        if (!$product || count($product) != count($shop)) {
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }

        $orderId = \common\models\comm\CommOrder::createOrderId($uid);
        $countPrice = 0;
        try {
            foreach ($product as $item) {

                $num = $item->num - $item->sell;
                if ($num <= 0) {
                    //$this->asJson(widgets\Response::error("已售罄"));
                    throw new Exception("已售罄");
                }

                if ($shop[$item->id] > $num) {
                    //$this->asJson(widgets\Response::error("库存不足"));
                    //return;
                    throw new Exception("库存不足");
                }

                if (!$item->status) {
                    //$this->asJson(widgets\Response::error("已下架"));
                    //return;
                    throw new Exception("已下架");
                }


                $model = new \common\models\comm\CommOrder();
                $model->user_id = $uid;
                $model->order_id = $orderId;
                $model->product_id = $item->id;
                $model->num = $shop[$item->id];
                $model->price = $item->price;
                $model->pay_price = $item->price;
                $model->address = (string)$addres;
                $model->status = \common\models\comm\CommOrder::status_waiting_pay;

                $ret = $model->save();
                $countPrice += $item->price;
            }
        } catch (Exception $ex) {
            $this->asJson(widgets\Response::error($ex->getMessage()));
        }

        $userInfo = \common\models\user\User::findOne($uid);
        $product = (object)[];
        $product->title = "Lipze订单-购买用户:{{$userInfo->username}}"; 
        $product->order_id = $orderId;
        $product->price = $countPrice;

        $order = \frontend\components\WxpayAPI\Pay::pay($openid['open_id'], $product);
        if (!$order['prepay_id'] || $order['return_code'] == "FAIL") {
            $this->asJson(widgets\Response::error("下单失败"));
            return;
        }
        //var_dump($order);exit;
        $out['id'] = $model->getPrimaryKey();
        $out['nonceStr'] = $order['nonce_str'];
        $out['package'] = "prepay_id={$order['prepay_id']}";
        $out['sign'] = $order['paySign'];
        $out["timeStamp"] = (string)time();
        $this->asJson(widgets\Response::sucess($out));
    }

    /**
     * 付款通知
     */
    public function actionNotice() {

        $userId = 1;
        $orderId = 111;
        $price = 10;
        $wx = new \frontend\components\WxpayAPI\PayNotify();
        return $wx->Handle(false);

        try {
            (new \frontend\service\Pay())->storage($orderId, $price);
        } catch (Exception $exc) {
            echo $exc->getMessage();
            \frontend\service\Error::addLog($userId, $exc->getMessage(), json_encode($exc->errorInfo));
            exit;
        }
    }

}
