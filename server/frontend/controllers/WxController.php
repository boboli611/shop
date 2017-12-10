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
    
   
    public function actionLogin(){
        
        /**
         * 
         * X-WX-Code:061l48B81dBNxP1WoCy81CzRA81l48Bo
X-WX-Encrypted-Data:AKQ9R/HnTLUw/etsX+ow1BQWTSqmnLhMJWSkC+kHNIHurxJhap52VpazaP4HBG4AiMHHjgwKVYQ3LXn7RZEBoRiTnEVMlBOogmSruG+x5XsU18tRct1xuNeJJSm++njQ/U88/aTdFRS6fjlGUvVi585jRVKSXwt8isK7ySfIxiiZ6Ehzki32EmRPfVsweGkf9McENF8jNjLY9LITCgW5+aBwbHog44+hPPtznRwCP/JXgl5/mDfduzDTE9WkOjTMY4ooCb1hEkq99hufTWnVBC4OCw3U88HcQAP1f0Ew0dgtuOChwT/WgjzthpOSQTwgpGFSgJqMYVDHVeeWBkKHk0bZcmbUVm4rjksNPI2upawqFwPVMyjjnmrroGlOGlI9xEYGdtzhFZxvqm+hDjaqB+P7a0zCSDuqLO4YGU4Ha5CdVmIdufkoQ9Gv2f2SQNsOn/rd363BJ9m0TFrCjLvVoc3SBfi5eSDkFspyXdgvmg+3B58oZMgOgm8+Hf2aKXGE
X-WX-IV:wVvxbYA35JXFSshZlrt6Gg==
         */
 
        $appid = \yii::$app->params["wx"]['appId'];
        $secret = \yii::$app->params["wx"]['appSecret'];
        $code = $_SERVER['HTTP_X_WX_CODE'];
        
        $grant_type = "authorization_code";
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appid&secret=$secret&js_code=$code&grant_type=$grant_type";
        $result = widgets\Http::Get($url);
        //$result = '{"openid": "OPENID111","session_key": "SESSIONKEY111","expires_in": 2592000}';
        $result = json_decode($result, true);
        var_dump($result);exit;
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
