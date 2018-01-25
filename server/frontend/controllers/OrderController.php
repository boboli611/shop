<?php

namespace frontend\controllers;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\widgets;

/**
 * index controller
 */
class OrderController extends Controller {
    
    public function actionList(){
        
        $page = (int) Yii::$app->request->get("p");
        $type = (int) Yii::$app->request->get("type");
        if (!$type) {
            $this->asJson(widgets\Response::error("参数错误"));
            return;
        }
        
        $uid = widgets\User::getUid();
        $list = \common\models\comm\CommOrder::getByUser($uid, $type, $page);
        
        $info['id'] = 1;
        $info['order_id'] = 6666;
        $info['goodsList'][1]['cover'] = 'https://lipz-shop.oss-cn-hangzhou.aliyuncs.com/upload/image/20180123/84d1464bd5ccee0cfa0d399702cd8817.jpg';
        $info['goodsList'][1]['title'] = 'xisdofso';
        $out['data'] =  $list;
        $this->asJson(widgets\Response::sucess($out));
    }
}