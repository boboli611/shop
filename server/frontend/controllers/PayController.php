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

/**
 * Site controller
 */
class ShopController extends Controller {

    public function actionShopPay() {
        
        $ids = Yii::$app->request->post("id");
        $addressId = Yii::$app->request->post("address_id");
        $content = Yii::$app->request->post("content");
        $ticketId = Yii::$app->request->post("ticket_id");
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
        if (!$shopList){
            $this->asJson(widgets\Response::error("商品数据错误"));
            return;
        }
        
        foreach ($shopList as $val) {
            $pIds[$val->storage_id] = $val->num;
        }

        try {
            $order= (new \frontend\service\Pay())->add($uid, $pIds, $addressId, $ticketId, $content);
        } catch (Exception $ex) {
            $this->asJson(widgets\Response::error($ex->getMessage()));
        }
        
        //$out['id'] = $model->getPrimaryKey();
        $out['nonceStr'] = $order['nonce_str'];
        $out['package'] = "prepay_id={$order['prepay_id']}";
        $out['sign'] = $order['paySign'];
        $out["timeStamp"] = (string) time();
        $this->asJson(widgets\Response::sucess($out));
    }

}
