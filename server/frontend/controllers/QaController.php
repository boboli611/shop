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
class QaController extends Controller {
    
    public function actionAdd()
    {
        $content = Yii::$app->request->post('content');
        $phone = Yii::$app->request->post('phone');
        if (!$content){
            $this->asJson(widgets\Response::error("内容不能为空"));
            return;
        }
        
        if (!$phone){
            $this->asJson(widgets\Response::error("联系方式不能为空"));
            return;
        }
        
        
        $model = new \common\models\comm\CommQa();
        
        $uid = widgets\User::getUid();
        $model->content = $content;
        $model->user_id = $uid;
        $model->phone = $phone;

        $id = $model->save();
        if (!$id) {
            $this->asJson(widgets\Response::error("提交失败"));
            return;
        } 
        $this->asJson(widgets\Response::sucess(['id' => $model->getPrimaryKey()]));
    }
}