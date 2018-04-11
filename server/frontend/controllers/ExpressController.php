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
class ExpressController extends Controller {
    
    public function actionGet(){
        $id =  Yii::$app->request->get("id");
        if (!$id){
            $this->asJson(widgets\Response::error("单号不能为空"));
            return false;
        }
        
        $info = \common\models\comm\CommExpressLog::find()->where(['no' => $id])->one();
        if (!$info){
            $this->asJson(widgets\Response::error("数据为空"));
            return false;
        }
        
        $info['content'] = json_decode($info['content'], TRUE);
        
        $this->asJson($info);
    }
}