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
use yii\log\Logger;
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

        $code = \yii::$app->request->post('code');
        $encryptedData = \yii::$app->request->post('encrypted_data');
        $iv = \yii::$app->request->post('iv');

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

        $pc = new \common\components\WxpayAPI\WXBizDataCrypt($appid, $result['session_key']);
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

            $data['nickName'] = $this->filterNickname($data['nickName']);
            $data['nickName'] = !$data['nickName'] ? "新用户" : $data['nickName'];
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
        $pids = Yii::$app->request->post("id");
        $addressId = Yii::$app->request->post("address_id");
        $content = Yii::$app->request->post("content");
        $ticketId = (int) Yii::$app->request->post("ticket_id");
        $num = (int) Yii::$app->request->post("buy_num");
        $num = $num > 0 ? $num : 1;

        if (!$pids) {
            $this->asJson(widgets\Response::error("商品id错误"));
            return;
        }

        if (!$addressId) {
            $this->asJson(widgets\Response::error("请选择地址"));
            return;
        }

        $uid = widgets\User::getUid();
        $addres = \common\models\user\UserAddress::findOne($addressId);
        if (!$addres || $addres->user_id != $uid) {
            $this->asJson(widgets\Response::error("非法参数"));
            return;
        }

        $trans = \common\models\comm\CommOrder::getDb()->beginTransaction();
        try {
            $pids = [$pids => $num];
            $order = (new \frontend\service\Pay())->add($uid, $pids, $addressId, $ticketId, $content);
            $trans->commit();
        } catch (Exception $ex) {
            $trans->rollBack();
            $this->asJson(widgets\Response::error($ex->getMessage()));
            return;
        }

        $out['order_id'] = $order['order_id'];
        $out['nonceStr'] = $order['nonce_str'];
        $out['package'] = "prepay_id={$order['prepay_id']}";
        $out['sign'] = $order['paySign'];
        $out["timeStamp"] = (string) time();
        $this->asJson(widgets\Response::sucess($out));
    }

    public function actionOrderPay() {

        $orderId = Yii::$app->request->post("id");
        if ($orderId) {
            $this->asJson(widgets\Response::error("非法参数"));
            return;
        }

        $info = \common\models\comm\CommOrder::find()->where(['order_id' => $orderId])->one();
    }

    /**
     * 付款通知
     */
    public function actionNotice() {
        try {
            $wx = new \common\components\WxpayAPI\PayNotify();
            $wx->Handle(false);
        } catch (\Exception $exc) {
            echo $exc->getMessage();
            \frontend\service\Error::addLog($userId, $exc->getMessage(), json_encode($exc->errorInfo));
            exit;
        }
    }

    private function filterNickname($nickname) {
        
        $nickname = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $nickname);

        $nickname = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $nickname);

        $nickname = str_replace(array('"', '\''), '', $nickname);

        return addslashes(trim($nickname));
    }

}
