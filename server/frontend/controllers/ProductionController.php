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
        
        $key = Yii::$app->request->post("key");
        $page = (int)Yii::$app->request->post("page");
        $orderField = (int)Yii::$app->request->post("order_field");
        $order = (int)Yii::$app->request->post("order");
        
        $out = \frontend\service\Product::search([], $key, $orderField, $order, $page);


        foreach ($out as &$item){
            $item['cover'] = "https://w3.hoopchina.com.cn/fe/68/7f/fe687fac1d41dfa75d03aaaf1ae176d8002.png";
        }
        //Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
        //return ['code'=>false,'message'=>["aaaa"]];
        return $this->asJson(widgets\Response::sucess($out));
    }
    
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionDetail() {
        
        $id = (int)Yii::$app->request->get("id");
        if (!$id){
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }

        
        $info = \common\models\comm\CommProduct::findOne($id);


        $out["gallery"][0]["id"] = 1;
        $out["gallery"][0]["img_url"] = "https://ss0.bdstatic.com/70cFvHSh_Q1YnxGkpoWK1HF6hhy/it/u=1353498151,4264969287&fm=27&gp=0.jpg";
        //Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
        //return ['code'=>false,'message'=>["aaaa"]];
        $out["info"] = $info;
        return $this->asJson(widgets\Response::sucess($out));
    }

    
    
   
}
