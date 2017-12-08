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
class ProductionController extends Controller {

    public function init() {
        $this->enableCsrfValidation = false;
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex() {
        $out = \common\models\CommProduct::getListByPage(8, 1);


        //Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
        //return ['code'=>false,'message'=>["aaaa"]];
        return $this->asJson(widgets\Response::sucess($out));
    }

    public function actionCreateOrder() {

        $pid = Yii::$app->request->post("product_id");
        $num = abs(Yii::$app->request->post("num"));
        $addressId = Yii::$app->request->post("adress_id");
              
        if (!$pid || !is_numeric($pid) || !is_numeric($addressId) || $num < 1){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
 
        $product = \common\models\comm\CommProduct::findOne($pid);
        if (!$product) {
            $this->asJson(widgets\Response::error("商品不存在"));
            return;
        }

        if ($product->count == 0){
             $this->asJson(widgets\Response::error("已售罄"));
             return;
        }

        if ($product->count <= $num){
             $this->asJson(widgets\Response::error("库存不足"));
             return;
        }
        
        if (!$product->status){
             $this->asJson(widgets\Response::error("已下架"));
             return;
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
        $ou['prepay_id'] = $order['prepay_id'];
        $this->asJson(widgets\Response::sucess($out));
    }
    
   
}
