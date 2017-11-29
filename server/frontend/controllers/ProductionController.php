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
        //var_dump(Yii::$app->request->post());exit;
      
        $pid = Yii::$app->request->post("product_id");
        $num = Yii::$app->request->post("num");
        if (!$pid || !is_numeric($pid)){
            $this->asJson(widgets\Response::error("参数错误"));
        }
        
        $product = \common\models\CommProduct::getDetail($pid);
        if (!$product) {
            $this->asJson(widgets\Response::error("商品不存在"));
        }
        
        if ($product->count == 0){
             $this->asJson(widgets\Response::error("已售罄"));
        }

        if ($product->count <= $num){
             $this->asJson(widgets\Response::error("库存不足"));
        }
        
        $model = new \common\models\comm\commOrder();
        $model->user_id = widgets\User::getUid();
        $model->product_id = $pid;
        $model->price = $product->price;
        $model->pay_price = $product->price;
        $model->status = \common\models\comm\commOrder::status_add;
        
        $ret = $model->save();
        var_dump($ret);
    }

}
