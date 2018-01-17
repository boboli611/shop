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

        
        /**
         * 
         * X-WX-Code:061l48B81dBNxP1WoCy81CzRA81l48Bo
          X-WX-Encrypted-Data:AKQ9R/HnTLUw/etsX+ow1BQWTSqmnLhMJWSkC+kHNIHurxJhap52VpazaP4HBG4AiMHHjgwKVYQ3LXn7RZEBoRiTnEVMlBOogmSruG+x5XsU18tRct1xuNeJJSm++njQ/U88/aTdFRS6fjlGUvVi585jRVKSXwt8isK7ySfIxiiZ6Ehzki32EmRPfVsweGkf9McENF8jNjLY9LITCgW5+aBwbHog44+hPPtznRwCP/JXgl5/mDfduzDTE9WkOjTMY4ooCb1hEkq99hufTWnVBC4OCw3U88HcQAP1f0Ew0dgtuOChwT/WgjzthpOSQTwgpGFSgJqMYVDHVeeWBkKHk0bZcmbUVm4rjksNPI2upawqFwPVMyjjnmrroGlOGlI9xEYGdtzhFZxvqm+hDjaqB+P7a0zCSDuqLO4YGU4Ha5CdVmIdufkoQ9Gv2f2SQNsOn/rd363BJ9m0TFrCjLvVoc3SBfi5eSDkFspyXdgvmg+3B58oZMgOgm8+Hf2aKXGE
          X-WX-IV:wVvxbYA35JXFSshZlrt6Gg==
         */
        $appid = \yii::$app->params["wx"]['appId'];
        $secret = \yii::$app->params["wx"]['appSecret'];
        $code = $_SERVER['HTTP_X_WX_CODE'];
        $encryptedData = $_SERVER["HTTP_X_WX_ENCRYPTED_DATA"];
        $iv = $_SERVER["HTTP_X_WX_IV"];
        //var_dump($code, $encryptedData, $iv,$_SERVER);
/*
        $appid = 'wx4f4bc4dec97d474b';
        $code = "123";
        $encryptedData = "CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                        QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                        9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                        3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                        NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                        evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                        /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                        u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                        /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                        8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                        C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                        6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                        /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                        lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                        oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                        20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                        Db/XcxxmK01EpqOyuxINew==";

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';
*/
        if (!$code || !$encryptedData || !$iv) {
            $this->asJson(widgets\Response::error("登录失败1"));
            return;
        }

        $grant_type = "authorization_code";
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=$grant_type";
        $result = widgets\Http::Get($url);
        //$result = '{"openid": "OPENID111","session_key": "SESSIONKEY111","expires_in": 2592000}';
        $result = json_decode($result, true);
        //var_dump($result);exit;
        //$result = [];
        //$result["session_key"] = 'tiihtNczf5v6AKRyjwEUhQ==';
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
            $model->token = md5($data["openId"] . time(). rand(1, 1000));
            $model->unionId = $data["unionId"];
            $model->session_key = $result["session_key"];
            $model->expires_in = time() + 3600 * 24 * 10;
            $model->save();
            
            $userModel = \common\models\user\User::findOne($model->user_id);
            if (!$userModel){
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
            throw  $ex;
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

        $pid = Yii::$app->request->post("ids");
        //$num = abs(Yii::$app->request->post("num"));
        //$addressId = Yii::$app->request->post("adress_id");
              
        if (!$pid){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
 
        $product = \common\models\comm\CommProductionStorage::getByids($pid);
        if (!$product) {
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }

        foreach ($product as $item){
            if ($item->num == 0){
             $this->asJson(widgets\Response::error("已售罄"));
             return;
            }

            if ($item->sell >= $item->num){
                 $this->asJson(widgets\Response::error("库存不足"));
                 return;
            }

            if (!$item->status){
                 $this->asJson(widgets\Response::error("已下架"));
                 return;
            }
        }
        
                
        $userId = widgets\User::getUid();
        $adress = \common\models\user\UserAddress::findOne($addressId);
        if ($adress->user_id != $userId){
            $this->asJson(widgets\Response::error("地址错误"));
        }
        
        $openid = \common\models\user\UserWxSession::findOne($userId);
        if (!$openid){
            $this->asJson(widgets\Response::error("未登录"));
        }
        
        $model = new \common\models\comm\CommOrder();
        $model->user_id = $userId;
        $model->order_id = \common\models\comm\CommOrder::createOrderId($userId);
        $model->product_id = $pid;
        $model->num = $num;
        $model->price = $product->price;
        $model->pay_price = $product->price;
        $model->adress_id = $addressId;
        $model->status = \common\models\comm\CommOrder::status_add;
 
        if(!$model->save()){
            $this->asJson(widgets\Response::error("购买失败"));
        }
        
        
        $order =  \frontend\components\WxpayAPI\Pay::pay($openid['open_id'],$product);
        if (!$order['prepay_id'] || $order['return_code'] == "FAIL"){
            $this->asJson(widgets\Response::error("下单失败"));
        }

        $out['id'] =  $model->getPrimaryKey();
        $out['nonceStr'] = $order['prepay_id'];
        $out['package'] = $order['prepay_id'];
        $out["timeStamp"] = time();
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
