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
    
    
    public function actionTest(){
        var_dump(__LINE__);
        \frontend\components\WxpayAPI\Pay::pay();
        echo "aaa";
    }

    public function actionLogin(){
 
        $appid = \yii::$app->params["wx"]['appId'];
        $secret = \yii::$app->params["wx"]['appSecret'];
        $code = "123";
        $grant_type = "authorization_code";
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=$grant_type";
        $result = widgets\Http::Get($url);
        $result = '{"openid": "OPENID111","session_key": "SESSIONKEY111","expires_in": 2592000}';
        $result = json_decode($result, true);
        if (!$result || isset($result['errcode'])){
            $this->asJson(widgets\Response::error("登录失败"));
            return;
        }
        $model = \common\models\user\UserWxSession::getByOpendId($result["openid"]);
        if (!$model){
            $model = new \common\models\user\UserWxSession();
        }
        $model->open_id = $result["openid"];
        $model->session_key = $result["session_key"];
        $model->expires_in = $result["expires_in"];

        $model->save();
        $this->asJson(widgets\Response::sucess($model));
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
